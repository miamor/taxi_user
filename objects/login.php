<?php
class Login extends Config {

    private $table_name = "Taxi";

    public function __construct() {
		parent::__construct();
	}

    public function login () {
        $query = "SELECT
					*
				FROM
					" . $this->table_name . "
				WHERE username = ?";
        $stmt = $this->conn->prepare($query);
		$stmt->bindParam(1, $this->username);

		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);

        // set values
        if ($row['id']) {
            if ( hash('sha256', $this->password) === $row['password']) {
                unset($row['password']);
                $this->id = $row['id'];
                $this->taxiInfo = $row;
                return true;
            }
        }

        return false;
    }

}

 ?>
