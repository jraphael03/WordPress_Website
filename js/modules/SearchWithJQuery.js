import $ from "jquery";

class Search {
  // Any code created inside of the constructor function will be executed as soon as we create an object using our class or blueprint
  // 1. describe and create/initiate our object
  constructor() {
    // Open and close search overlay
    this.addSearchHTML();
    this.openButton = $(".js-search-trigger");
    this.closeButton = $(".search-overlay__close");
    this.searchOverlay = $(".search-overlay");
    this.searchField = $("#search-term");

    this.events(); // Call event listeners

    this.isOverlayOpen = false;
    this.isSpinnerVisible = false;
    this.previousValue;
    this.typingTimer;

    // Results of our search
    this.resultsDiv = $("#search-overlay__results");
  }

  // 2. events
  events() {
    // Open and close search overlay
    this.openButton.on("click", this.openOverlay.bind(this)); // bind changes "this" to focus on the method
    this.closeButton.on("click", this.closeOverlay.bind(this));

    // Key response | s opens searchbar | esc closes
    $(document).on("keydown", this.keyPressDispatcher.bind(this)); // document grabs entire page

    // Target input to search when typing is finished
    this.searchField.on("keyup", this.typingLogic.bind(this));
  }

  // 3. Methods (function or action...)

  // When finished typing display search options
  typingLogic() {
    if (this.searchField.val() != this.previousValue) {
      // Only if the search field is changed will we run the code (Allows for arrow pressing)
      clearTimeout(this.typingTimer); // Everytime a key is pressed we reset the timer

      if (this.searchField.val()) {
        // If searchField is not blank run code
        if (!this.isSpinnerVisible) {
          // if isSpinnerVisible is false do the following
          this.resultsDiv.html('<div class="spinner-loader"></div>'); // Add a spinner to results
          this.isSpinnerVisible = true;
        }
        this.typingTimer = setTimeout(this.getResults.bind(this), 750); // Arg you want to run, how long to run
      } else {
        this.resultsDiv.html(""); // If searchField is empty display nothing, and stop spinner
        this.isSpinnerVisible = false;
      }
    }
    this.previousValue = this.searchField.val();
  }

  // Result finder
  getResults() {
    // Grab universityData from funcitons.php dyanamic    // /wp-json/university/v1/search?term= [Created in search-route.php]
    $.getJSON(
      universityData.root_url +
        "/wp-json/university/v1/search?term=" +
        this.searchField.val(),
      (results) => {
        // 2 args, url we want to send request to, function you want to run
        this.resultsDiv.html(`
        <div class="row">
          <div class="one-third">
            <h2 class="search-overlay__section-title">General Information</h2>
            ${
              results.generalInfo.length // Take results and look for generalInfo [generalInfo comes from search-route.php]
                ? '<ul class="link-list min-list">'
                : "<p>No General Information Matches That Search.</p>"
            }
            ${results.generalInfo
              .map((item) => {
                `<li><a href="${item.permalink}">${item.title.rendered}</a> ${
                  // items we are pulling comes from search-route.php
                  item.postType == "post" ? `by ${item.authorName}` : ""
                }</li>`;
              })
              .join("")}
          ${results.generalInfo.length ? "</ul>" : ""}
          </div>
          <div class="one-third">
            <h2 class="search-overlay__section-title">Programs</h2>
              ${
                results.programs.length // Take results and look for generalInfo [generalInfo comes from search-route.php]
                  ? '<ul class="link-list min-list">'
                  : `<p>No Programs Match That Search. <a href="${universityData.root_url}/programs">View All Programs</a></p>`
              }
              ${results.programs
                .map((item) => {
                  `<li><a href="${item.permalink}">${item.title.rendered}</a></li>`;
                })
                .join("")}
              ${results.programs.length ? "</ul>" : ""}

              

            <h2 class="search-overlay__section-title">Professors</h2>

            ${
              results.professors.length // Take results and look for generalInfo [generalInfo comes from search-route.php]
                ? '<ul class="professor-cards">'
                : `<p>No Professors Match That Search.</p>`
            }
              ${results.professors
                .map((item) => {
                  `
                    <li class="professor-card__list-item">
                        <a class="professor-card" href="${item.permalink}">
                            <img class="professor-card__image" src="${item.image}">
                            <span class="professor-card__name">${item.title}</span>
                        </a>
                    </li>
                  `;
                })
                .join("")}
              ${results.professors.length ? "</ul>" : ""}

          </div>
          <div class="one-third">
            <h2 class="search-overlay__section-title">Campuses</h2>
                ${
                  results.campuses.length // Take results and look for generalInfo [generalInfo comes from search-route.php]
                    ? '<ul class="link-list min-list">'
                    : `<p>No Campuses match That Search. <a href="${universityData.root_url}/campuses">View All Campuses</a></p>`
                }
              ${results.campuses
                .map((item) => {
                  `<li><a href="${item.permalink}">${item.title.rendered}</a></li>`;
                })
                .join("")}
              ${results.campuses.length ? "</ul>" : ""}


            <h2 class="search-overlay__section-title">Events</h2>

            ${
              results.events.length // Take results and look for generalInfo [generalInfo comes from search-route.php]
                ? ""
                : `<p>No Events match That Search. <a href="${universityData.root_url}/events">View All Events</a></p>`
            }
              ${results.events
                .map((item) => {
                  `
                    <div class="event-summary">
                        <a class="event-summary__date t-center" href="${item.permalink}">

                            <span class="event-summary__month">${item.month}</span>
                            <span class="event-summary__day">${item.day}</span>

                        </a>
                        <div class="event-summary__content">
                            <h5 class="event-summary__title headline headline--tiny"><a href="${item.permalink}">${item.title}</a></h5>

                            <p>${item.description}<a href="${item.permalink}" class="nu gray">Learn more</a></p>

                        </div>
                    </div>
                  `;
                })
                .join("")}
          </div>
        </div>
      `);
        this.isSpinnerVisible = false;
      }
    );
  }

  // Open overlay by keypress and close by keypress
  keyPressDispatcher(e) {
    //console.log(e.keyCode);

    // if keyCode is s and isOverlayOpen is false | open overlay  | if an input field or text area is focus disable s key
    if (
      e.keyCode == 83 &&
      !this.isOverlayOpen &&
      !$("input, textarea").is(":focus")
    ) {
      this.openOverlay();
    }

    // if keycode is esc and isOverlayOpen is true | close overlay
    if (e.keyCode == 27 && this.isOverlayOpen) {
      this.closeOverlay();
    }
  }

  // Open and close search overlay
  openOverlay() {
    this.searchOverlay.addClass("search-overlay--active");
    $("body").addClass("body-no-scroll");
    this.searchField.val(""); // Empties field when overlay is closed
    setTimeout(() => this.searchField.focus(), 301); // Automatically focus search bar
    console.log("open method ran");
    this.isOverlayOpen = true;
    return false;
  }

  closeOverlay() {
    this.searchOverlay.removeClass("search-overlay--active");
    $("body").removeClass("body-no-scroll");
    console.log("close method ran");
    this.isOverlayOpen = false;
  }

  // Adding search HTML to the bottom of our body
  addSearchHTML() {
    $("body").append(`
          <div class="search-overlay">
            <div class="search-overlay__top">
              <div class="container">
                <i class="fa fa-search search-overlay__icon" aria-hidden="true"></i>
                <input type="text" class="search-term" placeholder="What are you looking for?" id="search-term" autocomplete="off">
                <i class="fa fa-window-close search-overlay__close" aria-hidden="true"></i>
              </div>
            </div>
          </div>
    `);
  }
}

export default Search;
