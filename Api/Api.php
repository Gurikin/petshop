<?php
/**
 * Created by PhpStorm.
 * User: gurik
 * Date: 27.09.2018
 * Time: 21:05
 */

namespace db;

/**
 * Class DBWork
 * @package db
 */
class Api extends DBConnect
{
    /**
     * @var _db
     * @var allowTables
     */
    private $_db;
    private $allowTables = [''];
    private $_participantCount;

    /**
     * DBWork constructor.
     */
    public function __construct() {
        $this->setAllowTables();
        $this->setParticipantCount();
        parent::__construct();
        $this->_db = parent::getDb();
    }

    /**
     * @return array
     */
    public function getAllowTables() {
        return $this->allowTables;
    }

    /**
     * @param array $allowTables
     */
    public function setAllowTables($allowTables=['News','Session']) {
        $this->allowTables = $allowTables;
    }

    public function getParticipantCount() {
        return $this->_participantCount;
    }

    public function setParticipantCount($count=50) {
        $this->_participantCount = $count;
    }


    public function getResponse ($body, $status = "ok", $message = "") {

        $response = [
            "status" => $status,
            "payload" => $body,
            "message" => $message
            ];
        return json_encode($response);
    }

    /**
     * @param $table : array
     * @param null $id
     * @return array
     */
    public function Table($table, $id = null) {
        if (!in_array($table, $this->getAllowTables())) {
            return false;
        }
        $body[] = null;
        try {
            if ($id == null) {
                $query = "SELECT * FROM " . $table;
            } else {
                $query = "SELECT * FROM " . $table . "WHERE id=" . $id;
            }
            $resultSelect = $this->_db->query($query);
            if ($resultSelect === false) {
                return $this->getResponse($body,"error",$ex= new \PDOException);
            }
            while ($row = $resultSelect->fetch(\PDO::FETCH_ASSOC)) {
                $body [] = $row;
            }
            return $this->getResponse($body);
        } catch (\PDOException $ex) {
            return $this->getResponse($body,"error", $ex->getMessage());
        }
    }

    public function SessionSubscribe($email, $sessionId=1) {
        $body[] = null;
        try {
            $countQuery = "SELECT COUNT(*) FROM participant";
            $count = $this->_db->query($countQuery)->fetch(\PDO::FETCH_ASSOC)['COUNT(*)'];
            if ($count>$this->getParticipantCount()) {
                return $this->getResponse(null,"ok","Sorry, but there's no room. You can join to another session.");
            }
            $userQuery = "SELECT * FROM participant WHERE Email='$email'";
            $userCheck = $this->_db->query($userQuery);
            if ($userCheck === false) {
                return $this->getResponse($body,"error",$ex= new \PDOException);
            }
            $user = $userCheck->fetch(\PDO::FETCH_ASSOC);
            $sessionSubscibeQuery = "INSERT INTO session_participant (`participant_id`, `session_id`) VALUES ('".$user['ID']."', '".$sessionId."')";
            $sessionSubscibeResult = $this->_db->query($sessionSubscibeQuery);
            if ($sessionSubscibeResult === false) {
                return $this->getResponse($body,"ok", "Sorry, you need to sign up to join to the session.");
            }
            while ($row = $sessionSubscibeResult->fetch(\PDO::FETCH_ASSOC)) {
                $body [] = $row;
            }
            return $this->getResponse($body,"ok", "Thanks, you are successfully registered!");
        } catch (\PDOException $ex) {
            return $this->getResponse($body,"error", $ex->getMessage());
        }
    }
}