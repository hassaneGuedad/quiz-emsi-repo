<?php
include 'db_connect.php';

class UserList {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function displayUserList() {
        echo '<div class="col-lg-12">
                <div class="card card-outline card-success">
                    <div class="card-header">
                        <div class="card-tools">
                            <a class="btn btn-block btn-sm btn-default btn-flat border-primary" href="./index.php?page=new_user"><i class="fa fa-plus"></i> Add New User</a>
                        </div>
                        <div class="float-right">
                            <button class="btn btn-success btn-sm" onclick="printTable()"><i class="fa fa-print"></i> Imprimer</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-hover table-bordered" id="list">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th>Name</th>
                                    <th>Contact #</th>
                                    <th>Role</th>
                                    <th>Email</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>';
        $this->displayUsers();
        echo '          </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <script>
                $(document).ready(function () {
                    $(\'#list\').dataTable()
                    $(\'.view_user\').click(function () {
                        uni_modal("<i class=\'fa fa-id-card\'></i> User Details", "view_user.php?id=" + $(this).attr(\'data-id\'))
                    })
                    $(\'.delete_user\').click(function () {
                        _conf("Are you sure to delete this user?", "delete_user", [$(this).attr(\'data-id\')])
                    })
                })

                function delete_user($id) {
                    start_load()
                    $.ajax({
                        url: \'ajax.php?action=delete_user\',
                        method: \'POST\',
                        data: {
                            id: $id
                        },
                        success: function (resp) {
                            if (resp == 1) {
                                alert_toast("Data successfully deleted", \'success\')
                                setTimeout(function () {
                                    location.reload()
                                }, 1500)
                            }
                        }
                    })
                }

                function printTable() {
                    var printContents = document.getElementById("list").outerHTML;
                    var originalContents = document.body.innerHTML;

                    document.body.innerHTML = printContents;

                    window.print();

                    document.body.innerHTML = originalContents;
                }
            </script>';
    }

    private function displayUsers() {
        $i = 1;
        $type = array('', "Admin", "Staff", "Subscriber");
        $qry = $this->conn->query("SELECT *,concat(lastname,', ',firstname,' ',middlename) as name FROM users ORDER BY concat(lastname,', ',firstname,' ',middlename) ASC");
        while ($row = $qry->fetch_assoc()) {
            echo '<tr>
                    <th class="text-center">' . $i++ . '</th>
                    <td><b>' . ucwords($row['name']) . '</b></td>
                    <td><b>' . $row['contact'] . '</b></td>
                    <td><b>' . $type[$row['type']] . '</b></td>
                    <td><b>' . $row['email'] . '</b></td>
                    <td class="text-center">
                        <button type="button" class="btn btn-default btn-sm btn-flat border-info wave-effect text-info dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
                            Action
                        </button>
                        <div class="dropdown-menu" style="">
                            <a class="dropdown-item view_user" href="javascript:void(0)" data-id="' . $row['id'] . '">View</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="./index.php?page=edit_user&id=' . $row['id'] . '">Edit</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item delete_user" href="javascript:void(0)" data-id="' . $row['id'] . '">Delete</a>
                        </div>
                    </td>
                </tr>';
        }
    }
}

// Example usage:
$userList = new UserList($conn);
$userList->displayUserList();
?>
