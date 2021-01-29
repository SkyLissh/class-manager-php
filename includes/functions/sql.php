<?php require_once('connection.php') ?>

<?php 
    class SQL {
        //LogIn Or SignIn Queries
        public static function signIn (&$username, &$mail, &$password) {
            try {
                $connection = Connector::getConnection();
                $stmt = $connection->prepare('INSERT INTO teacher(username, mail, pass) VALUES (?,?,?)');
                $stmt->execute(array($username, $mail, $password));

                if ($stmt->rowCount() == 1) {
                    $response = array(
                        'response' => 'success',
                        'mail' => $mail,
                        'id' => $connection->lastInsertId()
                    );
                    return json_encode($response);
                } else {
                    $response = array(
                        'response' => 'error'
                    );
                    return json_encode($response);
                }
            } catch (\Throwable $th) {
                $response = array(
                    'response' => $th->getMessage()
                );
                return json_encode($response);
            }
        }

        public static function logIn (&$mail, &$password) {
            try {
                $connection = Connector::getConnection();
                $stmt = $connection->prepare('SELECT * FROM teacher WHERE mail=?');
                $stmt->execute(array($mail));
                $rs = $stmt->fetch();

                if ($rs) {
                    if (password_verify($password, $rs['pass'])) {
                        session_start();
                        $_SESSION['mail'] = $rs['mail'];
                        $_SESSION['username'] = $rs['username'];
                        $_SESSION['id'] = $rs['id'];
                        $_SESSION['logged'] = true;

                        $response = array(
                            'response' => 'success',
                            'mail' => $rs['mail'],
                            'id' => $rs['id']
                        );
                        return json_encode($response);
                    } else {
                        $response = array(
                            'response' => 'incorrect data'
                        );
                        return json_encode($response);
                    }
                } else {
                    $response = array(
                        'response' => 'incorrect data'
                    );
                    return json_encode($response);
                }
            } catch (\Throwable $th) {
                $response = array(
                    'response' => $th->getMessage()
                );
                return json_encode($response);
            }
        }

        public static function repeatedMail ($mail) {
            try {
                $connection = Connector::getConnection();
                $stmt = $connection->prepare('SELECT count(id) FROM teacher WHERE  mail=?');
                $stmt->execute(array($mail));
                $rs = $stmt->fetch();

                if ($rs['count(id)'] == 0) {
                    return true;
                } else {
                    return false;
                }
            } catch (\Throwable $th) {
                echo 'Error: ' . $th->getMessage();
                return false;
            }
        }

        //Class Queries
        public static function repeatedClassName(&$className) {
            try {
                $connection = Connector::getConnection();
                $stmt = $connection->prepare('SELECT count(id) FROM class WHERE  class_name=?');
                $stmt->execute(array($className));
                $rs = $stmt->fetch();

                if ($rs['count(id)'] == 0) {
                    return true;
                } else {
                    return false;
                }
            } catch (\Throwable $th) {
                echo 'Error: ' . $th->getMessage();
                return false;
            }
        }

        public static function addClass(&$className, &$id) {
            try {
                $connection = Connector::getConnection();
                $stmt = $connection->prepare('INSERT INTO class(class_name, id_teacher) VALUES (?, ?)');
                $stmt->execute(array($className, $id));

                if ($stmt->rowCount() == 1) {
                    $response = array(
                        'response' => 'success',
                        'class_name' => $className,
                        'id_teacher' => $id,
                        'id_inserted' => $connection->lastInsertId()
                    );
                    return json_encode($response);
                } else {
                    $response = array(
                        'response' => 'error'
                    );
                    return json_encode($response);
                }
            } catch (\Throwable $th) {
                $response = array(
                    'response' => $th->getMessage()
                );
                return json_encode($response);
            }
        }

        public static function selectClasses(&$id) {
            try {
                $connection = Connector::getConnection();
                $stmt = $connection->prepare('SELECT id, class_name FROM class WHERE id_teacher=?');
                $stmt->execute(array($id));
                $rs = $stmt->fetchAll();

                if ($rs) {
                    return $rs;
                } else {
                    return array();
                }
            } catch (\Throwable $th) {
                echo 'Error: ' . $th->getMessage();
                return array();
            }
        }

        public static function deleteClass(&$id) {
            try {
                $connection = Connector::getConnection();
                $stmt = $connection->prepare('DELETE FROM students WHERE id_class=?');

                if ($stmt->execute(array($id))) {
                    $stmt = $connection->prepare(('DELETE FROM class WHERE id=?'));
                    $stmt->execute(array($id));

                    if ($stmt->rowCount() == 1) {
                        $response = array(
                            'response' => 'success'
                        );
                        return json_encode($response);
                    } else {
                        $response = array(
                            'response' => 'error'
                        );
                        return json_encode($response);
                    }
                } else {
                    $response = array(
                        'response' => 'error'
                    );
                    return json_encode($response);
                }
            } catch (\Throwable $th) {
                $response = array(
                    'response' => $th->getMessage()
                );
                return json_encode($response);
            }
        }

        public static function verifyClass(&$id) {
            try {
                $connection = Connector::getConnection();
                $stmt = $connection->prepare('SELECT count(id) FROM class WHERE id=?');
                $stmt->execute(array($id));
                $rs = $stmt->fetch();

                if ($rs['count(id)'] != 0) {
                    return true;
                } else {
                    return false;
                }
            } catch (\Throwable $th) {
                echo 'Error: ' . $th->getMessage();
                return false;
            }
        }

        //Students Queries
        public static function repeatedStudent(&$mail, &$id) {
            try {
                $connection = Connector::getConnection();
                $stmt = $connection->prepare('SELECT count(id) FROM students WHERE  mail=? AND id_class=?');
                $stmt->execute(array($mail, $id));
                $rs = $stmt->fetch();

                if ($rs['count(id)'] == 0) {
                    return true;
                } else {
                    return false;
                }
            } catch (\Throwable $th) {
                echo 'Error: ' . $th->getMessage();
                return false;
            }
        }

        public static function addStudent(&$name, &$lastName, &$mail, &$id) {
            try {
                $connection = Connector::getConnection();
                $stmt = $connection->prepare('INSERT INTO students(name, last_name, mail, id_class) VALUES (?,?,?,?)');
                $stmt->execute(array($name, $lastName, $mail, $id));

                if ($stmt->rowCount() == 1) {
                    $response = array(
                        'response' => 'success',
                        'name' => $name,
                        'last_name' => $lastName,
                        'mail' => $mail,
                        'id_inserted' => $connection->lastInsertId()
                    );
                    return json_encode($response);
                } else {
                    $response = array(
                        'response' => 'error'
                    );
                    return json_encode($response);
                }
            } catch (\Throwable $th) {
                $response = array(
                    'response' => $th->getMessage()
                );
                return json_encode($response);
            }
        }

        public static function selectStudents(&$id) {
            try {
                $connection = Connector::getConnection();
                $stmt = $connection->prepare('SELECT id, name, last_name, mail FROM students WHERE id_class=?');
                $stmt->execute(array($id));
                $rs = $stmt->fetchAll();

                if ($rs) {
                    return $rs;
                } else {
                    return array();
                }
            } catch (\Throwable $th) {
                echo 'Error: ' . $th->getMessage();
                return array();
            }
        }

        public static function deleteStudent(&$id, &$idClass) {
            try {
                $connection = Connector::getConnection();
                $stmt = $connection->prepare('DELETE FROM students WHERE id=? AND id_class=?');
                $stmt->execute(array($id, $idClass));

                if ($stmt->rowCount() == 1) {
                    $response = array(
                        'response' => 'success'
                    );
                    return json_encode($response);
                } else {
                    $response = array(
                        'response' => 'error'
                    );
                    return json_encode($response);
                }
            } catch (\Throwable $th) {
                $response = array(
                    'response' => $th->getMessage()
                );
                return json_encode($response);
            }
        }

        public static function selectStudentInfo(&$id) {
            try {
                $connection = Connector::getConnection();
                $stmt = $connection->prepare('SELECT name, last_name, mail FROM students WHERE id=?');
                $stmt->execute(array($id));
                $rs = $stmt->fetch();

                if ($rs) {
                    $response = array(
                        'response' => 'success',
                        'name' => $rs['name'],
                        'last_name' => $rs['last_name'],
                        'mail' => $rs['mail']
                    );
                    return json_encode($response);
                } else {
                    $response = array(
                        'response' => 'error'
                    );
                    return json_encode($response);
                }
            } catch (\Throwable $th) {
                $response = array(
                    'response' => $th->getMessage()
                );
                return json_encode($response);
            }
        }

        public static function editStudent(&$name, &$lastName, &$mail,&$idStudent, &$idClass) {
            try {
                $connection = Connector::getConnection();
                $stmt = $connection->prepare('UPDATE students SET name=?, last_name=?, mail=? WHERE id=? AND id_class=?');
                $stmt->execute(array($name, $lastName, $mail ,$idStudent, $idClass));

                if ($stmt->rowCount()) {
                    $response = array(
                        'response' => 'success',
                        'name' => $name,
                        'last_name' => $lastName,
                        'mail' => $mail
                    );
                    return json_encode($response);
                } else {
                    $response = array(
                        'response' => 'error'
                    );
                    return json_encode($response);
                }
            } catch (\Throwable $th) {
                $response = array(
                    'response' => $th->getMessage()
                );
                return json_encode($response);
            }
        }
    }
?>