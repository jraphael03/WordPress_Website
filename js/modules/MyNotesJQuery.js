import $ from "jquery";

class MyNotes {
  constructor() {
    this.events();
  }

  events() {
    $("#my-notes").on("click", ".delete-note", this.deleteNote); // my-notes checks for existing and new notes, add an extra arg between to look for already existing classes as well
    $("#my-notes").on("click", ".edit-note", this.editNote.bind(this));
    $("#my-notes").on("click", ".update-note", this.updateNote.bind(this));
    $(".submit-note").on("click", this.createNote.bind(this));
  }

  // Methods will go here

  // POST METHOD    (create note)
  createNote(e) {
    var ourNewPost = {
      // We need to pass the content that wp is expecting, we are looking to update our title and or content field, this is the data we are passing along
      title: $(".new-note-title").val(), // Creating a new title to send to our db
      content: $(".new-note-body").val(),
      status: "publish", // status is automatically set to draft
    };

    $.ajax({
      beforeSend: (xhr) => {
        xhr.setRequestHeader("X-WP-Nonce", universityData.nonce); // What wordpress has to be on the look out for, then the data that needs to be passed in
      },
      url: universityData.root_url + "/wp-json/wp/v2/note/", // First part of url was generated in Search.js, we are creating a note so end of url needs to be pointed at note (other Exs: posts,pages)
      type: "POST",
      data: ourNewPost, // We need to send data to the backend
      success: (response) => {
        $(".new-note-title, .new-note-body").val(""); // After creating a new post empty the values in our form
        $(`
            <li data-id="${response.id}">
                <input readonly class="note-title-field" value="${response.title.raw}">

                <span class="edit-note"><i class="fa fa-pencil" aria-hidden="true"></i>Edit</span>
                <span class="delete-note"><i class="fa fa-trash-o" aria-hidden="true"></i>Delete</span>

                <textarea readonly class="note-body-field">${response.content.raw}</textarea>     

                <span class="update-note btn btn--blue btn--small"><i class="fa fa-arrow-right" aria-hidden="true"></i>Save</span>
            </li>
        `)
          .prependTo("#my-notes")
          .hide()
          .slideDown(); // HTML we want to use to create a note, what element we want to prepend note to, animate note to come into view gradually by hiding, then sliding down
        console.log("Congrats");
        console.log(response);
      },
      error: (response) => {
        if (response.responseText == "You have reached your note limit.") {
          $(".note-limit-message").addClass("active");
        }
        console.log("Sorry");
        console.log(response);
      },
    });
  }

  // Edit button (works with update)
  editNote(e) {
    var thisNote = $(e.target).parents("li"); // grab e from param, take the target e and garb the parent li which contains the id #

    if (thisNote.data("state") == "editable") {
      this.makeNoteReadOnly(thisNote); // make read only
    } else {
      this.makeNoteEditable(thisNote); // make editable
    }
  }

  makeNoteEditable(thisNote) {
    // Remove readonly attr on fields when edit button is clicked
    thisNote
      .find(".note-title-field, .note-body-field")
      .removeAttr("readonly")
      .addClass("note-active-field");
    // Add blue save button when edit button is clicked
    thisNote.find(".update-note").addClass("update-note--visible");
    // Turn the edit button into a cancel button when edit button is clicked
    thisNote
      .find(".edit-note")
      .html('<i class="fa fa-times" aria-hidden="true"></i>Cancel');
    thisNote.data("state", "editable"); // Change state
  }

  makeNoteReadOnly(thisNote) {
    // Add readonly value to edit button when cancel or save is clicked
    thisNote
      .find(".note-title-field, .note-body-field")
      .attr("readonly", "readonly")
      .removeClass("note-active-field");
    // Remove blue save button, and cancel button, when cancel button or save button is clicked
    thisNote.find(".update-note").removeClass("update-note--visible");
    // Click the cancel button and reset all normal features
    thisNote
      .find(".edit-note")
      .html('<i class="fa fa-pencil" aria-hidden="true"></i>Edit');
    thisNote.data("state", "cancel"); // Change state
  }

  // POST METHOD    (update note)
  updateNote(e) {
    var thisNote = $(e.target).parents("li"); // grab e from param, take the target e and garb the parent li which contains the id #

    var ourUpdatedPost = {
      // We need to pass the content that wp is expecting, we are looking to update our title and or content field, this is the data we are passing along
      title: thisNote.find(".note-title-field").val(), // thisNote to find our html class and grab the value of that class
      content: thisNote.find(".note-body-field").val(),
    };

    $.ajax({
      beforeSend: (xhr) => {
        xhr.setRequestHeader("X-WP-Nonce", universityData.nonce); // What wordpress has to be on the look out for, then the data that needs to be passed in
      },
      url:
        universityData.root_url + "/wp-json/wp/v2/note/" + thisNote.data("id"), // First part of url was generated in Search.js, even though attr was named data-id when using jquery data we don't need to add data
      type: "POST",
      data: ourUpdatedPost, // We need to send data to the backend
      success: (response) => {
        this.makeNoteReadOnly(thisNote); // After saving the note if it was sucessful turn it into readonly, thisNote selects the proper note
        console.log("Congrats");
        console.log(response);
      },
      error: (response) => {
        console.log("Sorry");
        console.log(response);
      },
    });
  }

  // DELETE METHOD
  deleteNote(e) {
    var thisNote = $(e.target).parents("li"); // grab e from param, take the target e and garb the parent li which contains the id #

    $.ajax({
      beforeSend: (xhr) => {
        xhr.setRequestHeader("X-WP-Nonce", universityData.nonce); // What wordpress has to be on the look out for, then the data that needs to be passed in
      },
      url:
        universityData.root_url + "/wp-json/wp/v2/note/" + thisNote.data("id"), // First part of url was generated in Search.js, even though attr was named data-id when using jquery data we don't need to add data
      type: "DELETE",
      success: (response) => {
        thisNote.slideUp(); // Remove from page using a slideUp animation
        console.log("Congrats");
        console.log(response);
        if (response.userNoteCount < 5) {
          // When deleting a note if count is less than 5 remove message
          $(".note-limit-message").removeClass("active");
        }
      },
      error: (response) => {
        console.log("Sorry");
        console.log(response);
      },
    });
  }
}

export default MyNotes;
