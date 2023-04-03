// When logout button will be clicked this ajax request will be pushed.
$('#deleteBtn').click(function (event) {
  event.preventDefault();
  $.ajax({
    url: '/logout',
    data: { 'flag': true },
    beforeSend: function () {
      showLoader();
    },
    success: function (response) {
      if (response.msg) {
        //on the success user will be redirected to the login page.
        window.location.href = '/login';
        hideLoader();
      }
    },
    complete: function () {
      hideLoader();
    }
  });
});

function takeNote() {
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
    url: '/storeNote',
    type: "POST",
    data: {
      text: inputVal,
      title:  titleVal
    },
    beforeSend: function () {
      showLoader();
    },
    success: function (response) {
      // If server successfully updated the comment.
      if (response) {
        addNotes(response);
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

// Getting all notes on the page loads.
$.ajax({
  url: '/fetchAllNotes',
  type: "POST",
  beforeSend: function () {
    showLoader();
  },
  success: function (response) {
    // If server successfully updated the comment.
    response.forEach(element => {
      addNotes(element);
    });
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

function addNotes(note) {

  var content = note.content.substring(0, 100);

  const listNotes = document.querySelector('.list-notes');

  const noteLi = document.querySelector(`#note${note.id}`);

  if (noteLi !== null) {
    return;
  }

  const divItem = document.createElement('li');
  divItem.className = 'list-group-item note';
  divItem.id = `note${note.id}`;

  // Insert the card details into the li.
  divItem.innerHTML = `
    <div onclick="showNote(${note.id})">
      <h4>${note.title}</h4>
      <p>${content}</p>
      <span>${new Date(note.createdTime.date)}</span>
    </div>
    `;
  listNotes.insertBefore(divItem, listNotes.firstChild);
}

function showNote(noteId) {

  $.ajax({
    url: '/fetchSingeNote',
    type: "POST",
    data: {
      id: noteId,
    },
    beforeSend: function () {
      showLoader();
    },
    success: function (response) {
      // If server successfully updated the comment.
      if(response) {
        location.href = '/editPost?postId=' + response;
      }
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


