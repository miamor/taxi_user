<?php
class Trip extends Config {

    private $table_name = "Trip";

    public function __construct() {
		parent::__construct();
	}


    public function create () {
        //write query
			$query = "INSERT INTO
					" . $this->table_name . "
				SET
					name = ?, phone = ?, addressfrom = ?, addressto = ?, PNR = ?, time = ?, seat = ?, is_round = ?, details = ?, num_guess = ?, price = ?";

		$stmt = $this->conn->prepare($query);

		// posted values
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->phone = htmlspecialchars(strip_tags($this->phone));
        $this->from = htmlspecialchars(strip_tags($this->from));
        $this->to = htmlspecialchars(strip_tags($this->to));
        $this->PNR = htmlspecialchars(strip_tags($this->PNR));
        $this->time = htmlspecialchars(strip_tags($this->time));
        $this->seat = htmlspecialchars(strip_tags($this->seat));
        $this->is_round = htmlspecialchars(strip_tags($this->is_round));
        $this->num_guess = htmlspecialchars(strip_tags($this->num_guess));
		$this->details = content($this->details);

        // bind parameters
		$stmt->bindParam(1, $this->name);
        $stmt->bindParam(2, $this->phone);
        $stmt->bindParam(3, $this->from);
        $stmt->bindParam(4, $this->to);
        $stmt->bindParam(5, $this->PNR);
        $stmt->bindParam(6, $this->time);
        $stmt->bindParam(7, $this->seat);
        $stmt->bindParam(8, $this->is_round);
        $stmt->bindParam(9, $this->details);
        $stmt->bindParam(10, $this->num_guess);
        $stmt->bindParam(11, $this->price);

        // execute the query
		if ($stmt->execute()) {
			return true;
		} else
			return false;
    }


    public function update () {

		$query = "UPDATE
					" . $this->table_name . "
				SET
					name = :name,
					des = :des
				WHERE
					id = :id";

		$stmt = $this->conn->prepare($query);

		// posted values
		$this->name = htmlspecialchars(strip_tags($this->name));
		$this->description = content($this->description);
		$this->id = htmlspecialchars(strip_tags($this->id));

		// bind parameters
		$stmt->bindParam(':name', $this->name);
		$stmt->bindParam(':des', $this->des);
		$stmt->bindParam(':id', $this->id);

		// execute the query
		if ($stmt->execute()) return true;
		else return false;
	}


	public function delete () {

		$query = "DELETE FROM " . $this->table_name . " WHERE id = ?";

		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(1, $this->id);

        // execute the query
		if ($result = $stmt->execute()) return true;
		else return false;
	}


    public function readAll () {
        $now = date('Y-m-d');
        //$todayNotTakenList = $this->readAll_today_not_taken();

        $query = "SELECT
    					*
    				FROM
    					" . $this->table_name . "
    				WHERE
                        status = 0
                        AND time > '{$now}'
                        AND phone = ?
                    ORDER BY
                        CASE WHEN approve = 1 then 1 else 2 end,
                        CASE WHEN time LIKE '{$now}%' then 1 else 2 end,
                        time ASC, id ASC";

		$stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->user_phone);
		$stmt->execute();

		$all_list = array();
        $approve = array();

		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $row['is_round_txt'] = ($row['is_round'] ? '2 chiều' : null);
            $row['is_one_round'] = ($row['is_round'] ? 0 : 1);
            $row['class'] = '';
            if ($row['approve']) {
                $row['class'] .= ' approved';
            }
            if (strpos($row['time'], $now)) {
                $row['class'] .= ' today';
            }
            $all_list[] = $row;
        }
//        echo json_encode($all_list);

        $this->all_list = array('data'=>$all_list, 'trips_num'=>count($all_list));
        return $this->all_list;
    }

    public function countAll () {
        $now = date('Y-m-d');
        $today = $this->count_today();
        $myPriority = $this->count_priority();

        $query = "SELECT
					id,status,`time`,prioritize
				FROM
					" . $this->table_name . "
                WHERE
                    status = 0
                    AND time > '{$now}'
                    AND phone = ?";

		$stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->user_phone);
		$stmt->execute();
        $all = $stmt->rowCount()+$today+$myPriority;
        return $all;
    }

    public function readOne () {
        $query = "SELECT
					*
				FROM
					" . $this->table_name . "
				WHERE id = ?";
        $stmt = $this->conn->prepare($query);
		$stmt->bindParam(1, $this->id);

		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row['id']) {
            $row['is_round_txt'] = ($row['is_round'] ? '2 chiều' : '1 chiều');
            $row['is_one_round'] = ($row['is_round'] ? "0" : "1");
        }

        // set values
		$this->id = $row['id'];
        $this->taxiID = $row['taxiid'];
        $this->coin = $row['coin'];

        return ($row['id'] ? $row : null);
    }




    public function readAllBuy_today () {
        $now = date('Y-m-d');

        $query = "SELECT
    				*
    			FROM
    				" . $this->table_name . "
    			WHERE
                    taxiid = {$this->taxiID}
                    AND time LIKE '{$now}%'
                ORDER BY
                    status ASC, time ASC, id ASC";

    	$stmt = $this->conn->prepare($query);
    	$stmt->execute();

    	$all_list = array();

    	while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $row['is_round_txt'] = ($row['is_round'] ? '2 chiều' : null);
            $row['is_one_round'] = ($row['is_round'] ? 0 : 1);
            $fromAr = array_values(array_filter(explode(',', $row['addressfrom'])));
            $toAr = array_values(array_filter(explode(',', $row['addressto'])));
            $row['addressfrom'] = trim(implode(',', array_slice($fromAr, -2, 2, true)));
            $row['addressto'] = trim(implode(',', array_slice($toAr, -2, 2, true)));
            $all_list[] = $row;
        }
        return $all_list;
    }

    public function readAllBuy () {
        $now = date('Y-m-d');
        $todayList = $this->readAllBuy_today();

        $query = "SELECT
    					*
    				FROM
    					" . $this->table_name . "
    				WHERE
                        taxiid = {$this->taxiID}
                        AND time NOT LIKE '{$now}%'
                    ORDER BY
                        status ASC, time ASC, id ASC";

		$stmt = $this->conn->prepare($query);
		$stmt->execute();

		$all_list = array();

		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $row['is_round_txt'] = ($row['is_round'] ? '2 chiều' : null);
            $row['is_one_round'] = ($row['is_round'] ? 0 : 1);
            $fromAr = array_values(array_filter(explode(',', $row['addressfrom'])));
            $toAr = array_values(array_filter(explode(',', $row['addressto'])));
            $row['addressfrom'] = trim(implode(',', array_slice($fromAr, -2, 2, true)));
            $row['addressto'] = trim(implode(',', array_slice($toAr, -2, 2, true)));
            $all_list[] = $row;
        }

        $this->all_list = array('today'=>$todayList,
         'others'=>$all_list);
        return $this->all_list;
    }


    public function changeStt ($stt) {
        $query = "UPDATE
        			" . $this->table_name . "
        		SET
        			status = 1, taxiid = ?
        		WHERE
        			id = ?";

    	$stmt = $this->conn->prepare($query);

    	// posted values
        $stmt->bindParam(1, $this->taxiID);
        $stmt->bindParam(2, $this->id);

        // execute the query
    	if ($stmt->execute()) return true;
    	else return false;
    }

    public function buy ($coin) {
        $this->changeStt(1);
        $now = date("Y-m-d H:i:s");
        $query = "INSERT INTO PayCoin SET taxiID = ?, tripID = ?, coin = ?, time = ?";
        $stmt = $this->conn->prepare($query);
        // bind parameters
    	$stmt->bindParam(1, $this->taxiID);
        $stmt->bindParam(2, $this->id);
        $stmt->bindParam(3, $coin);
        $stmt->bindParam(4, $now);

    	if ($stmt->execute()) {
            return 1;
        } else return 0;
    }

}

 ?>
