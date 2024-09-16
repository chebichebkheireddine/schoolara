DROP TABLE IF EXISTS absences;

CREATE TABLE `absences` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `date` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `student_id` (`student_id`),
  KEY `class_id` (`class_id`),
  CONSTRAINT `absences_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`),
  CONSTRAINT `absences_ibfk_2` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO absences VALUES('1','1','1','2024-07-13');
INSERT INTO absences VALUES('2','1','1','2024-07-31');



DROP TABLE IF EXISTS admins;

CREATE TABLE `admins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO admins VALUES('1','schoolara','schoolara');



DROP TABLE IF EXISTS classes;

CREATE TABLE `classes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `start_date` date NOT NULL,
  `number_of_months` int(11) NOT NULL,
  `num_group` int(11) DEFAULT NULL,
  `teacher_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `teacher_id` (`teacher_id`),
  CONSTRAINT `classes_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO classes VALUES('1','الحساب الذهني','2024-07-22','1','1','1');
INSERT INTO classes VALUES('2','احكام التجويد','2024-07-23','1','1','2');
INSERT INTO classes VALUES('3','تحفيظ القران و تدريس الاحكام  تحت 15 سنة','2024-07-23','1','1','3');
INSERT INTO classes VALUES('4',' تحفيظ القران و تدريس الاحكام تحت 15 سنة','2024-07-23','1','2','4');
INSERT INTO classes VALUES('5','تحفيظ القران و تدريس الاحكام فوق 15 سنة','2024-07-23','1','1','3');
INSERT INTO classes VALUES('6','تحفيظ القران و تدريس الاحكام فوق 15 سنة','2024-07-23','1','2','4');
INSERT INTO classes VALUES('7','Mathematique','2024-07-23','1','1','12');
INSERT INTO classes VALUES('8','Physique','2024-07-23','1','1','5');



DROP TABLE IF EXISTS students;

CREATE TABLE `students` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `class_id` int(11) DEFAULT NULL,
  `enrollment_date` date NOT NULL,
  `end_date` date NOT NULL,
  `payment_end` date DEFAULT NULL,
  `paid` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `class_id` (`class_id`),
  CONSTRAINT `students_ibfk_1` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO students VALUES('1','fffffff','1','2024-07-11','2024-08-11','','0');



DROP TABLE IF EXISTS teachers;

CREATE TABLE `teachers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO teachers VALUES('1','Amara Leila');
INSERT INTO teachers VALUES('2','Bouzidi Houari');
INSERT INTO teachers VALUES('3','Ben Aissa Nabila');
INSERT INTO teachers VALUES('4','Achour Meriem');
INSERT INTO teachers VALUES('5','Mechri');
INSERT INTO teachers VALUES('6','Dahman Houcine');
INSERT INTO teachers VALUES('7','Mouloudi');
INSERT INTO teachers VALUES('8','Negui zahra');
INSERT INTO teachers VALUES('9','Kalakhi Sabrine');
INSERT INTO teachers VALUES('10','Ouis Amine');
INSERT INTO teachers VALUES('11','Bahous Messani');
INSERT INTO teachers VALUES('12','Chiheb Karim');



