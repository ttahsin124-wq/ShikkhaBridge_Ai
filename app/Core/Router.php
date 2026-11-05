<?php
class Router
{
    private array $routes = [];

    public function add(string $method, string $pattern, array $handler): void
    {
        $regex = preg_replace(['#\{token\}#', '#\{[a-z0-9]+\}#i'], ['([a-f0-9]{64})', '(\d+)'], $pattern);
        $this->routes[] = [$method, '#^' . $regex . '$#', $handler];
    }

    public function get(string $p, array $h): void { $this->add('GET', $p, $h); }
    public function post(string $p, array $h): void { $this->add('POST', $p, $h); }

    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/';
        $base = base_path();
        if ($base !== '' && str_starts_with($path, $base)) $path = substr($path, strlen($base));
        $path = '/' . trim($path, '/');

        foreach ($this->routes as [$m, $regex, $handler]) {
            if ($m === $method && preg_match($regex, $path, $matches)) {
                if ($method === 'POST' && !Csrf::verify()) {
                    (new ErrorController())->show(419);
                    return;
                }
                array_shift($matches);
                [$class, $action] = $handler;
                $args = array_map(fn($x) => strlen($x) < 64 && ctype_digit($x) ? (int)$x : $x, $matches);
                (new $class())->$action(...$args);
                return;
            }
        }
        (new ErrorController())->show(404);
    }
}
