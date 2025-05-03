$(function(){
    // Check availability for a field
    function checkField(field){
      const val = field.val().trim();
      const feedback = field.is('#username')
        ? $('#username-feedback')
        : $('#email-feedback');
  
      // clear if empty
      if (!val) {
        feedback.text('');
        return;
      }
  
      const params = {};
      if (field.is('#username')) params.username = val;
      if (field.is('#email'))    params.email    = val;
  
      $.getJSON('check_availability.php', params)
        .done(function(resp){
          if (field.is('#username')) {
            feedback.text(resp.usernameTaken
              ? 'That username is already taken.'
              : ''
            );
          }
          if (field.is('#email')) {
            feedback.text(resp.emailTaken
              ? 'That email is already registered.'
              : ''
            );
          }
        })
        .fail(function(){
          console.error('Availability check failed');
        });
    }
  
    // Fire on blur (when the user leaves the field)
    $('#username').on('blur', function(){
      checkField($(this));
    });
  
    $('#email').on('blur', function(){
      checkField($(this));
    });
  });
  