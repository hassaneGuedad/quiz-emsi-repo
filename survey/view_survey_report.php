<?php
include 'db_connect.php';

class SurveyReport {
    private $conn;
    private $id;
    private $stitle;
    private $description;
    private $start_date;
    private $end_date;
    private $taken;
    private $answers;

    public function __construct($conn, $id) {
        $this->conn = $conn;
        $this->id = $id;
        $this->fetchSurveyDetails();
        $this->fetchSurveyAnswers();
    }

    private function fetchSurveyDetails() {
        $qry = $this->conn->query("SELECT * FROM survey_set WHERE id = " . $this->id)->fetch_array();
        foreach ($qry as $k => $v) {
            if ($k == 'title') {
                $k = 'stitle';
            }
            $this->$k = $v;
        }

        $this->taken = $this->conn->query("SELECT DISTINCT(user_id) FROM answers WHERE survey_id = {$this->id}")->num_rows;
    }

    private function fetchSurveyAnswers() {
        $this->answers = $this->conn->query("SELECT a.*, q.type FROM answers a INNER JOIN questions q ON q.id = a.question_id WHERE a.survey_id ={$this->id}");
    }

    public function displaySurveyReport() {
        echo '<style>
                .tfield-area{
                    max-height: 30vh;
                    overflow: auto;
                }
            </style>
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-md-4">
                        <div class="card card-outline card-primary">
                            <div class="card-header">
                                <h3 class="card-title"><b>Survey Details</b></h3>
                            </div>
                            <div class="card-body p-0 py-2">
                                <div class="container-fluid">
                                    <p>Title: <b>' . $this->stitle . '</b></p>
                                    <p class="mb-0">Description:</p>
                                    <small>' . $this->description . '</small>
                                    <p>Start: <b>' . date("M d, Y", strtotime($this->start_date)) . '</b></p>
                                    <p>End: <b>' . date("M d, Y", strtotime($this->end_date)) . '</b></p>
                                    <p>Have Taken: <b>' . number_format($this->taken) . '</b></p>
                                </div>
                                <hr class="border-primary">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="card card-outline card-success">
                            <div class="card-header">
                                <h3 class="card-title"><b>Survey Report</b></h3>
                                <div class="card-tools">
                                    <button class="btn btn-flat btn-sm bg-gradient-success" type="button" id="print"><i class="fa fa-print"></i> Print</button>
                                </div>
                            </div>
                            <div class="card-body ui-sortable">';
        $this->displayReportQuestions();
        echo '              </div>
                        </div>
                    </div>
                </div>
            </div>
            <script>
                $(\'#manage-survey\').submit(function(e){
                    e.preventDefault()
                    start_load()
                    $.ajax({
                        url:\'ajax.php?action=save_answer\',
                        method:\'POST\',
                        data:$(this).serialize(),
                        success:function(resp){
                            if(resp == 1){
                                alert_toast("Thank You.",\'success\')
                                setTimeout(function(){
                                    location.href = \'index.php?page=survey_widget\'
                                },2000)
                            }
                        }
                    })
                })
                $(\'#print\').click(function(){
                    start_load()
                    var nw = window.open("print_report.php?id=' . $this->id . '","_blank","width=800,height=600")
                        nw.print()
                        setTimeout(function(){
                            nw.close()
                            end_load()
                        },2500)
                })
            </script>';
    }

    private function displayReportQuestions() {
        $question = $this->conn->query("SELECT * FROM questions WHERE survey_id = $this->id ORDER BY ABS(order_by) ASC, ABS(id) ASC");
        while ($row = $question->fetch_assoc()) {
            echo '<div class="callout callout-info">
                    <h5>' . $row['question'] . '</h5>   
                    <div class="col-md-12">
                    <input type="hidden" name="qid[' . $row['id'] . ']" value="' . $row['id'] . '">    
                    <input type="hidden" name="type[' . $row['id'] . ']" value="' . $row['type'] . '">    
                        
                        ' . ($row['type'] != 'textfield_s' ? '<ul>' : '') . '';
            $this->displayReportAnswerOptions($row);
            echo '      </div>   
                </div>';
        }
    }

    private function displayReportAnswerOptions($row) {
        if ($row['type'] != 'textfield_s') {
            foreach (json_decode($row['frm_option']) as $k => $v) {
                $prog = ((isset($this->ans[$row['id']][$k]) ? count($this->ans[$row['id']][$k]) : 0) / $this->taken) * 100;
                $prog = round($prog, 2);
                echo '<li>
                        <div class="d-block w-100">
                            <b>' . $v . '</b>
                        </div>
                        <div class="d-flex w-100">
                            <span class="">' . (isset($this->ans[$row['id']][$k]) ? count($this->ans[$row['id']][$k]) : 0) . '/' . $this->taken . '</span>
                            <div class="mx-1 col-sm-8">
                                <div class="progress w-100" >
                                    <div class="progress-bar bg-primary progress-bar-striped" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: ' . $prog . '%">
                                        <span class="sr-only">' . $prog . '%</span>
                                    </div>
                                </div>
                            </div>
                            <span class="badge badge-info">' . $prog . '%</span>
                        </div>
                    </li>';
            }
            echo '</ul>';
        } else {
            echo '<div class="d-block tfield-area w-100 bg-dark">';
            if (isset($this->ans[$row['id']])) {
                foreach ($this->ans[$row['id']] as $val) {
                    echo '<blockquote class="text-dark">' . $val . '</blockquote>';
                }
            }
            echo '</div>';
        }
    }
}


if (isset($_GET['id'])) {
    $surveyReport = new SurveyReport($conn, $_GET['id']);
    $surveyReport->displaySurveyReport();
}
?>
