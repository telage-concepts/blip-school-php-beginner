<?php

session_start();

$quiz_questions = [
    [
        "text" => "What is the capital city of Nigeria?",
        "options" => ["Lagos", "Abuja", "Kano", "Port Harcourt"],
        "correct" => "Abuja"
    ],
    [
        "text" => "What does PHP stand for?",
        "options" => ["Personal Home Pager", "Programming Hypertext Protocol", "Hypertext Preprocessor"],
        "correct" => "Hypertext Preprocessor"
    ],
    [
        "text" => "Which symbol starts a variable name in PHP?",
        "options" => ["@", "#", "$", "&"],
        "correct" => "$"
    ],
];

$total_questions = count($quiz_questions);

if (isset($_GET['action']) && $_GET['action'] === 'reset') {
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit;
}

if (!isset($_SESSION['current_question_index'])) {
    $_SESSION['current_question_index'] = 0;
}

if (!isset($_SESSION['user_score'])) {
    $_SESSION['user_score'] = 0;
}

$feedback = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $submitted_index = (int) $_POST['question_index'];
    $answer_key = "q" . $submitted_index . "_answer";

    if (isset($_POST[$answer_key]) && !empty($_POST[$answer_key])) {

        $user_answer = htmlspecialchars($_POST[$answer_key], ENT_QUOTES, 'UTF-8');
        $correct_answer = $quiz_questions[$submitted_index]['correct'];

        if ($user_answer === $correct_answer) {
            $_SESSION['user_score']++;
            $feedback = "<p style='color:green;'>Correct! Well done.</p>";
        } else {
            $feedback = "<p style='color:red;'>Incorrect. The correct answer was: " . $correct_answer . "</p>";
        }

        $_SESSION['current_question_index']++;

    } else {
        $feedback = "<p style='color:orange;'>Please select an answer before submitting.</p>";
    }
}

$current_index = $_SESSION['current_question_index'];
$quiz_finished = $current_index >= $total_questions;

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Naija PHP Quiz</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <h1>Naija PHP Quiz!</h1>

    <?php if ($quiz_finished) { ?>

        <div class="results-container">
            <h2>Quiz Completed!</h2>
            <?php echo $feedback; ?>
            <p>Your final score is:
                <strong><?php echo $_SESSION['user_score']; ?></strong> out of <?php echo $total_questions; ?>
            </p>
            <hr>
            <a href="index.php?action=reset" class="reset-button">Start Over</a>
        </div>

    <?php } else { ?>

        <div class="quiz-container">
            <?php
            echo $feedback;

            $question_data = $quiz_questions[$current_index];
            $question_text = $question_data['text'];
            $question_name_attribute = "q" . $current_index . "_answer";
            ?>

            <form action="index.php" method="POST">
                <fieldset>
                    <legend>Question <?php echo ($current_index + 1); ?> of <?php echo $total_questions; ?>:
                        <?php echo $question_text; ?>
                    </legend>

                    <?php
                    foreach ($question_data['options'] as $index => $option_text) {
                        $option_id = "option_" . $current_index . "_" . $index;
                        ?>
                        <div>
                            <input type="radio" id="<?php echo $option_id; ?>" name="<?php echo $question_name_attribute; ?>"
                                value="<?php echo $option_text; ?>" required>
                            <label for="<?php echo $option_id; ?>"><?php echo $option_text; ?></label>
                        </div>
                        <?php
                    }
                    ?>
                </fieldset>

                <input type="hidden" name="question_index" value="<?php echo $current_index; ?>">
                <input type="submit" value="Submit Answer">
            </form>
            <hr>
            <p style="text-align: center; font-size: 0.9em;"><a href="index.php?action=reset">Reset Quiz</a></p>
        </div>

    <?php } ?>

</body>

</html>