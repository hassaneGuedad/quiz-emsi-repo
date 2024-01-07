<?php
include 'db_connect.php';

class UserDetails {
    private $conn;
    private $id;
    private $name;
    private $email;
    private $contact;
    private $address;
    private $type_arr = array('', 'Admin', 'Staff', 'Subscriber');

    public function __construct($conn, $id) {
        $this->conn = $conn;
        $this->id = $id;
        $this->fetchUserDetails();
    }

    private function fetchUserDetails() {
        if (isset($this->id)) {
            $qry = $this->conn->query("SELECT *, CONCAT(lastname, ', ', firstname, ' ', middlename) as name FROM users WHERE id = " . $this->id)->fetch_array();
            foreach ($qry as $k => $v) {
                $this->$k = $v;
            }
        }
    }

    public function displayUserInfo() {
        echo '<div class="container-fluid">
                <table class="table">
                    <tr>
                        <th>Name:</th>
                        <td><b>' . ucwords($this->name) . '</b></td>
                    </tr>
                    <tr>
                        <th>Email:</th>
                        <td><b>' . $this->email . '</b></td>
                    </tr>
                    <tr>
                        <th>Contact:</th>
                        <td><b>' . $this->contact . '</b></td>
                    </tr>
                    <tr>
                        <th>Address:</th>
                        <td><b>' . $this->address . '</b></td>
                    </tr>
                    <tr>
                        <th>User Role:</th>
                        <td><b>' . $this->type_arr[$this->type] . '</b></td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer display p-0 m-0">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>';
    }
}

// Example usage:
if (isset($_GET['id'])) {
    $userDetails = new UserDetails($conn, $_GET['id']);
    $userDetails->displayUserInfo();
}
?>
<style>
    #uni_modal .modal-footer {
        display: none
    }

    #uni_modal .modal-footer.display {
        display: flex
    }
</style>
