function updateNote(id) {
    // Each comment error span will contain the error message.
    var noteError = document.getElementById('error');

    // Input field of the comment.
    var inputVal = $('#addTxt').val();
    var titleVal = $('#title').val();
  
    // Check if input comment field is not empty.
    if (inputVal === "" || titleVal === "") {
      noteError.textContent = "Comment or title should not be empty.";
      return;
    }
  
    // Calling ajax and passing input field value and post Id.
    $.ajax({
      url: '/updateNote',
      type: "POST",
      data: {
        text: inputVal,
        title:  titleVal,
        id: id
      },
      beforeSend: function () {
        showLoader();
      },
      success: function (response) {
        // If server successfully updated the comment.
        if (response === true) {
          noteError.textContent = "Updated";
        }
        $('#addTxt').val("");
        $('#title').val("");
        hideLoader();
      },
      error: function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR.responseText);
        hideLoader();
      },
      complete: function () {
        hideLoader();
      }
    });
}

function deleteNote(id) { 
  location.href = "/removeNote/?noteId=" + id;
  // Calling ajax and passing input field value and post Id.
  $.ajax({
    url: '/deleteNote',
    type: "POST",
    data: {
      id: id
    },
    beforeSend: function () {
      showLoader();
    },
    success: function (response) {
      // If server successfully updated the comment.
      if (response === true) {
        alert("Deleted");
        location.href = '/home'
      }
      $('#addTxt').val("");
      $('#title').val("");
      hideLoader();
    },
    error: function (jqXHR, textStatus, errorThrown) {
      console.log(jqXHR.responseText);
      hideLoader();
    },
    complete: function () {
      hideLoader();
    }
  });


}