<?php
include 'db_connect.php';

class SurveyDetails {
    private $conn;
    private $id;
    private $stitle;
    private $description;
    private $start_date;
    private $end_date;
    private $answers;

    public function __construct($conn, $id) {
        $this->conn = $conn;
        $this->id = $id;
        $this->fetchSurveyDetails();
    }

    private function fetchSurveyDetails() {
        $qry = $this->conn->query("SELECT * FROM survey_set WHERE id = " . $this->id)->fetch_array();
        foreach ($qry as $k => $v) {
            if ($k == 'title') {
                $k = 'stitle';
            }
            $this->$k = $v;
        }

        $this->answers = $this->conn->query("SELECT DISTINCT(user_id) FROM answers WHERE survey_id = {$this->id}")->num_rows;
    }

    public function displaySurveyDetails() {
        echo '<div class="col-lg-12">
                <div class="row">
                    <div class="col-md-4">
                        <div class="card card-outline card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Survey Details</h3>
                            </div>
                            <div class="card-body p-0 py-2">
                                <div class="container-fluid">
                                    <p>Title: <b>' . $this->stitle . '</b></p>
                                    <p class="mb-0">Description:</p>
                                    <small>' . $this->description . '</small>
                                    <p>Start: <b>' . date("M d, Y", strtotime($this->start_date)) . '</b></p>
                                    <p>End: <b>' . date("M d, Y", strtotime($this->end_date)) . '</b></p>
                                    <p>Have Taken: <b>' . number_format($this->answers) . '</b></p>
                                </div>
                                <hr class="border-primary">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="card card-outline card-success">
                            <div class="card-header">
                                <h3 class="card-title"><b>Survey Questionnaire</b></h3>
                                <div class="card-tools">
                                    <button class="btn btn-block btn-sm btn-default btn-flat border-success new_question" type="button"><i class="fa fa-plus"></i> Add New Question</button>
                                </div>
                            </div>
                            <form action="" id="manage-sort">
                                <div class="card-body ui-sortable">';
        $this->displayQuestions();
        echo '                  </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>';
    }

    private function displayQuestions() {
        $question = $this->conn->query("SELECT * FROM questions WHERE survey_id = {$this->id} ORDER BY ABS(order_by) ASC, ABS(id) ASC");
        while ($row = $question->fetch_assoc()) {
            echo '<div class="callout callout-info">
                    <div class="row">
                        <div class="col-md-12">
                            <span class="dropleft float-right">
                                <a class="fa fa-ellipsis-v text-dark" href="javascript:void(0)" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></a>
                                <div class="dropdown-menu" style="">
                                    <a class="dropdown-item edit_question text-dark" href="javascript:void(0)" data-id="' . $row['id'] . '">Edit</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item delete_question text-dark" href="javascript:void(0)" data-id="' . $row['id'] . '">Delete</a>
                                </div>
                            </span>
                        </div>
                    </div>
                    <h5>' . $row['question'] . '</h5>
                    <div class="col-md-12">
                        <input type="hidden" name="qid[]" value="' . $row['id'] . '">';
            $this->displayAnswerOptions($row);
            echo '  </div>
                </div>';
        }
    }

    private function displayAnswerOptions($row) {
        if ($row['type'] == 'radio_opt') {
            foreach (json_decode($row['frm_option']) as $k => $v) {
                echo '<div class="icheck-primary">
                        <input type="radio" id="option_' . $k . '" name="answer[' . $row['id'] . ']" value="' . $k . '" checked="">
                        <label for="option_' . $k . '">' . $v . '</label>
                      </div>';
            }
        } elseif ($row['type'] == 'check_opt') {
            foreach (json_decode($row['frm_option']) as $k => $v) {
                echo '<div class="icheck-primary">
                        <input type="checkbox" id="option_' . $k . '" name="answer[' . $row['id'] . '][]" value="' . $k . '" >
                        <label for="option_' . $k . '">' . $v . '</label>
                      </div>';
            }
        } else {
            echo '<div class="form-group">
                    <textarea name="answer[' . $row['id'] . ']" id="" cols="30" rows="4" class="form-control" placeholder="Write Something Here..."></textarea>
                  </div>';
        }
    }
}

// Example usage:
if (isset($_GET['id'])) {
    $surveyDetails = new SurveyDetails($conn, $_GET['id']);
    $surveyDetails->displaySurveyDetails();
}
?>
<script>
    $(document).ready(function(){
        $('.ui-sortable').sortable({
            placeholder: "ui-state-highlight",
            update: function() {
                alert_toast("Saving question sort order.","info")
                $.ajax({
                    url: "ajax.php?action=action_update_qsort",
                    method: 'POST',
                    data: $('#manage-sort').serialize(),
                    success: function (resp) {
                        if (resp == 1) {
                            alert_toast("Question order sort successfully saved.","success")
                        }
                    }
                })
            }
        })
    })
    $('.new_question').click(function(){
        uni_modal("New Question","manage_question.php?sid=<?php echo $id ?>","large")
    })
    $('.edit_question').click(function(){
        uni_modal("New Question","manage_question.php?sid=<?php echo $id ?>&id="+$(this).attr('data-id'),"large")
    })

    $('.delete_question').click(function(){
        _conf("Are you sure to delete this question?","delete_question",[$(this).attr('data-id')])
    })

    function delete_question($id){
        start_load()
        $.ajax({
            url:'ajax.php?action=delete_question',
            method:'POST',
            data:{id:$id},
            success:function(resp){
                if(resp==1){
                    alert_toast("Data successfully deleted",'success')
                    setTimeout(function(){
                        location.reload()
                    },1500)
                }
            }
        })
    }
</script>
