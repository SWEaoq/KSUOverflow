// assets/js/script.js

$(function() {
    // 1) Load Latest Questions
    if ($('#questions-list').length) {
      $('#questions-list').load(
        'questions_ajax.php',
        function(responseTxt, statusTxt, xhr) {
          if (statusTxt === 'error') {
            $('#questions-list').html(
              `<div class="alert alert-danger">
                 Error loading questions: ${xhr.status} ${xhr.statusText}
               </div>`
            );
          }
        }
      );
    }
  
    // 2) Load Answers on the question page
    if ($('#answers-list').length) {
      const qid = new URLSearchParams(window.location.search).get('id');
      $('#answers-list').load(
        'answers_ajax.php?question_id=' + qid,
        function(responseTxt, statusTxt, xhr) {
          if (statusTxt === 'error') {
            $('#answers-list').html(
              `<div class="alert alert-danger">
                 Error loading answers: ${xhr.status} ${xhr.statusText}
               </div>`
            );
          }
        }
      );
    }
  });
  