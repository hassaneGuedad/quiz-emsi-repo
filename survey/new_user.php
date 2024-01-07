<?php

class UserForm
{
    private $id;
    private $firstname;
    private $middlename;
    private $lastname;
    private $contact;
    private $address;
    private $type;
    private $email;

    public function __construct($id = null, $firstname = '', $middlename = '', $lastname = '', $contact = '', $address = '', $type = 3, $email = '')
    {
        $this->id = $id;
        $this->firstname = $firstname;
        $this->middlename = $middlename;
        $this->lastname = $lastname;
        $this->contact = $contact;
        $this->address = $address;
        $this->type = $type;
        $this->email = $email;
    }

    public function displayForm()
    {
        ?>
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <form action="" id="manage_user">
                        <input type="hidden" name="id" value="<?= $this->id ?>">
                        <div class="row">
                            <div class="col-md-6 border-right">
                                <b class="text-muted">Personal Information</b>
                                <div class="form-group">
                                    <label for="" class="control-label">First Name</label>
                                    <input type="text" name="firstname" class="form-control form-control-sm" required value="<?= $this->firstname ?>">
                                </div>
                                <!-- ... (other fields) ... -->
                            </div>
                            <div class="col-md-6">
                                <b class="text-muted">System Credentials</b>
                                <?php if ($_SESSION['login_type'] == 1): ?>
                                    <div class="form-group">
                                        <label for="" class="control-label">User Role</label>
                                        <select name="type" id="type" class="custom-select custom-select-sm">
                                            <option value="3" <?= ($this->type == 3) ? 'selected' : '' ?>>Subscriber</option>
                                            <option value="2" <?= ($this->type == 2) ? 'selected' : '' ?>>Staff</option>
                                            <option value="1" <?= ($this->type == 1) ? 'selected' : '' ?>>Admin</option>
                                        </select>
                                    </div>
                                <?php else: ?>
                                    <input type="hidden" name="type" value="3">
                                <?php endif; ?>
                                <!-- ... (other fields) ... -->
                            </div>
                        </div>
                        <hr>
                        <div class="col-lg-12 text-right justify-content-center d-flex">
                            <button class="btn btn-primary mr-2">Save</button>
                            <button class="btn btn-secondary" type="button" onclick="location.href = 'index.php?page=user_list'">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <script>
            // JavaScript code remains unchanged
        </script>
        <?php
    }
}

// Example usage:
$userForm = new UserForm($id, $firstname, $middlename, $lastname, $contact, $address, $type, $email);
$userForm->displayForm();
?>
