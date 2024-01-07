<?php

include 'db_connect.php';

class SurveyManager {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function displaySurveyDetails($surveyId) {
        $surveyDetails = $this->conn->query("SELECT * FROM survey_set WHERE id = $surveyId")->fetch_assoc();
        if (!$surveyDetails) {
            echo "Survey not found.";
            return;
        }

        // Rename 'title' to 'stitle'
        if (isset($surveyDetails['title'])) {
            $surveyDetails['stitle'] = $surveyDetails['title'];
            unset($surveyDetails['title']);
        }

        $this->renderSurveyDetails($surveyDetails);
    }

    private function renderSurveyDetails($surveyDetails) {
        ?>
        <div class="col-lg-12">
            <!-- ... (Survey details HTML) ... -->
        </div>
        <script>
            // JavaScript code remains unchanged
        </script>
        <?php
    }

    public function displaySurveyQuestionnaire($surveyId) {
        $questions = $this->conn->query("SELECT * FROM questions WHERE survey_id = $surveyId ORDER BY abs(order_by) ASC, abs(id) ASC");

        if (!$questions->num_rows) {
            echo "No questions found for this survey.";
            return;
        }

        $this->renderSurveyQuestionnaire($questions);
    }

    private function renderSurveyQuestionnaire($questions) {
        ?>
        <div class="col-lg-8">
            <!-- ... (Survey questionnaire HTML) ... -->
        </div>
        <script>
            // JavaScript code remains unchanged
        </script>
        <?php
    }

    public function saveAnswer($postData) {
        // Handle saving answers to the database
        // You need to implement this part according to your database schema and requirements
        // ...

        // Example response
        echo 1;
    }
}

// Example usage:
$surveyManager = new SurveyManager($conn);

// Display survey details
$surveyManager->displaySurveyDetails($_GET['id']);

// Display survey questionnaire
$surveyManager->displaySurveyQuestionnaire($_GET['id']);
?>
