USE shikkhabridge;

INSERT INTO subjects (id, name) VALUES
 (1,'Mathematics'),(2,'Physics'),(3,'English'),(4,'ICT');

INSERT INTO topics (id, subject_id, name) VALUES
 (1,1,'Algebra'),(2,1,'Geometry'),
 (3,2,'Motion'),(4,2,'Energy'),
 (5,3,'Grammar'),(6,3,'Vocabulary'),
 (7,4,'Internet & Web'),(8,4,'Databases');

INSERT INTO questions (topic_id, question_text, option_a, option_b, option_c, option_d, correct_option, explanation) VALUES
 (1,'If x + 5 = 12, what is x?','5','7','12','17','B','Subtract 5 from both sides: x = 12 - 5 = 7.'),
 (1,'What is (a + b)^2 equal to?','a^2 + b^2','a^2 + 2ab + b^2','2a + 2b','a^2 - b^2','B','Expand (a+b)(a+b) = a^2 + 2ab + b^2.'),
 (2,'What is the sum of the angles of a triangle?','90 degrees','180 degrees','270 degrees','360 degrees','B','The interior angles of any triangle add up to 180 degrees.'),
 (2,'What is the area of a circle of radius r?','2 pi r','pi r^2','pi d','r^2','B','Area of a circle = pi x r x r.'),
 (3,'What is the SI unit of velocity?','m','m/s','m/s^2','kg','B','Velocity is distance per unit time, so metres per second.'),
 (3,'Acceleration is the rate of change of what?','Distance','Mass','Velocity','Force','C','Acceleration = change in velocity / time.'),
 (4,'What is the SI unit of energy?','Newton','Watt','Joule','Pascal','C','Energy is measured in joules (J).'),
 (4,'Which is the formula for kinetic energy?','mgh','1/2 m v^2','F x d','m x a','B','Kinetic energy = 1/2 x mass x velocity squared.'),
 (5,'What is the past tense of "go"?','goed','gone','went','going','C','"Went" is the irregular simple past of "go".'),
 (5,'Choose the correct sentence.','She go to school every day.','She goes to school every day.','She going to school every day.','She gone to school every day.','B','Third person singular present simple takes "goes".'),
 (6,'Which word is a synonym of "happy"?','Sad','Joyful','Angry','Tired','B','Joyful has a similar meaning to happy.'),
 (6,'Which word is an antonym of "ancient"?','Old','Modern','Historic','Dusty','B','Modern is the opposite of ancient.'),
 (7,'What does HTML stand for?','HyperText Markup Language','High Text Machine Language','Hyperlink Tool Mode Language','Home Tool Markup Language','A','HTML = HyperText Markup Language.'),
 (7,'Which protocol is used for secure web browsing?','HTTP','FTP','HTTPS','SMTP','C','HTTPS encrypts traffic using TLS.'),
 (8,'What does SQL stand for?','Structured Query Language','Simple Question Language','Sequential Query List','System Quality Language','A','SQL = Structured Query Language.'),
 (8,'Which key uniquely identifies each row in a table?','Foreign key','Primary key','Index key','Secondary key','B','A primary key uniquely identifies a row.');
