jQuery(document).ready(function ($) {
  window.copyLink = function (button) {
    // Get the link from the data-link attribute of the <i> tag
    var link = button.getAttribute("data-link");

    // Copy the link to the clipboard
    navigator.clipboard
      .writeText(link)
      .then(function () {
        console.log("Link copied to clipboard:", link);

        // Add a class to trigger the animation
        button.classList.add("copied");

        // Remove the class after the animation ends
        setTimeout(function () {
          button.classList.remove("copied");
        }, 3000); // Adjust the duration to match your animation
      })
      .catch(function (err) {
        console.error("Error in copying link: ", err);
      });
  };
  const notyf = new Notyf();
  const teamSelect = document.getElementById("wdm_team_name");
  const smeNameInput = document.getElementById("wdm_sme_name");

  if (teamSelect && smeNameInput) {
    teamSelect.addEventListener("change", function () {
      const selectedOption = teamSelect.options[teamSelect.selectedIndex];
      const leaderName = selectedOption.getAttribute("leader_name");

      console.log("Selected Value:", selectedOption.value);
      console.log("Leader Name:", leaderName);

      // Set the input value to the leader name
      smeNameInput.value = leaderName || "";
    });
  }

  // Get the input elements by their IDs
  const projectNameInput = document.getElementById("wdm_project_name");
  const clientNameInput = document.getElementById("wdm_client_name");
  const gitLinkInputCheckbox = document.getElementById("wdm_add_git_repo");
  let addProjectCheckbox = "";
  let checkboxLabel = "";
  let trackingTimeLinkText = "";
  let trackingTimeLinkLabel = "";
  let trackingTimeLinkInput = "";
  let addGitRepoCheckbox = "";
  let gitRepoLabel = "";
  let gitLinkText = "";
  let gitLinkLabel = "";
  let gitLinkInput = "";

  let addSonarRepoCheckbox = "";
  let sonarRepoLabel = "";
  let sonarLinkText = "";
  let sonarLinkLabel = "";
  let sonarLinkInput = "";

  let addSpinupRepoCheckbox = "";
  let spinupRepoLabel = "";
  let spinupLinkText = "";
  let spinupLinkLabel = "";
  let spinupLinkInput = "";

  function toggleSpinupSettings() {
    let spinupSettingsContainer = document.querySelector(".wdm-spinup-settings-container");
    console.log('hihihi')
    if (
      spinupSettingsContainer.style.display === "none" ||
      getComputedStyle(spinupSettingsContainer).display === "none"
    ) {
      spinupSettingsContainer.style.display = "flex"; // Set to flex before making it visible
      setTimeout(() => {
        spinupSettingsContainer.classList.remove("hidden");
      }, 10); // Small delay to allow display to apply
    } else {
      spinupSettingsContainer.classList.add("hidden");
      setTimeout(() => {
        spinupSettingsContainer.style.display = "none";
      }, 500); // Matches transition duration
    }
  }

  if (projectNameInput && clientNameInput) {
    addProjectCheckbox = document.getElementById(
      "wdm_add_project_tracking_time"
    );
    trackingTimeLinkInput = document.getElementById("wdm_tracking_time_link");
    checkboxLabel = document.querySelector(
      'label[for="wdm_add_project_tracking_time"]'
    );
    trackingTimeLinkLabel = document.querySelector(
      'label[for="wdm_tracking_time_link"]'
    );
    addGitRepoCheckbox = document.getElementById("wdm_add_git_repo");
    gitLinkInput = document.getElementById("wdm_git_link");
    gitRepoLabel = document.querySelector('label[for="wdm_add_git_repo"]');
    gitLinkLabel = document.querySelector('label[for="wdm_git_link"]');

    addSonarRepoCheckbox = document.getElementById("wdm_add_sonar");
    sonarLinkInput = document.getElementById("wdm_sonar_link");
    sonarRepoLabel = document.querySelector('label[for="wdm_add_sonar"]');
    sonarLinkLabel = document.querySelector('label[for="wdm_sonar_link"]');

    addSpinupRepoCheckbox = document.getElementById("wdm_add_spinup");
    spinupLinkInput = document.getElementById("wdm_spinup_link");
    spinupRepoLabel = document.querySelector('label[for="wdm_add_spinup"]');
    spinupLinkLabel = document.querySelector('label[for="wdm_spinup_link"]');

    // Add an onchange event listener
    addProjectCheckbox.addEventListener("change", function () {
      if (addProjectCheckbox.checked) {
        trackingTimeLinkInput.classList.add("wdm-disabled-input");
        trackingTimeLinkLabel.classList.add("wdm-disabled-label");
        trackingTimeLinkText = trackingTimeLinkInput.value;
        trackingTimeLinkInput.value = "";
      } else {
        trackingTimeLinkInput.classList.remove("wdm-disabled-input");
        trackingTimeLinkLabel.classList.remove("wdm-disabled-label");
        trackingTimeLinkInput.value = trackingTimeLinkText;
      }
    });

    addGitRepoCheckbox.addEventListener("change", function () {
      if (addGitRepoCheckbox.checked) {
        gitLinkInput.classList.add("wdm-disabled-input");
        gitLinkLabel.classList.add("wdm-disabled-label");
        gitLinkText = gitLinkInput.value;
        gitLinkInput.value = "";
      } else {
        gitLinkInput.classList.remove("wdm-disabled-input");
        gitLinkLabel.classList.remove("wdm-disabled-label");
        gitLinkInput.value = gitLinkText;
      }
    });

    addSonarRepoCheckbox.addEventListener("change", function () {
      if (addSonarRepoCheckbox.checked) {
        sonarLinkInput.classList.add("wdm-disabled-input");
        sonarLinkLabel.classList.add("wdm-disabled-label");
        sonarLinkText = sonarLinkInput.value;
        sonarLinkInput.value = "";
        
      } else {
        sonarLinkInput.classList.remove("wdm-disabled-input");
        sonarLinkLabel.classList.remove("wdm-disabled-label");
        sonarLinkInput.value = sonarLinkText;
      }
    });


    addSpinupRepoCheckbox.addEventListener("change", function () {
      toggleSpinupSettings();
      if (addSpinupRepoCheckbox.checked) {
        spinupLinkInput.classList.add("wdm-disabled-input");
        spinupLinkLabel.classList.add("wdm-disabled-label");
        spinupLinkText = spinupLinkInput.value;
        spinupLinkInput.value = "";
      } else {
        spinupLinkInput.classList.remove("wdm-disabled-input");
        spinupLinkLabel.classList.remove("wdm-disabled-label");
        spinupLinkInput.value = spinupLinkText;
      }
    });

    // Function to check if both client name and project name are not empty
    function checkInputs() {
      const projectNameValue = projectNameInput.value.trim();
      const clientNameValue = clientNameInput.value.trim();
      const currentGitLink = gitLinkInput.value.trim();
      const currentGitLinkCheckboxStatus = gitLinkInputCheckbox.checked;

      console.log("current ", currentGitLinkCheckboxStatus);
      console.log("current ", currentGitLink);

      if (currentGitLink !== "" || currentGitLinkCheckboxStatus) {
        console.log("enable");

        addSonarRepoCheckbox.disabled = false;
        addSonarRepoCheckbox.classList.remove("wdm-disabled-checkbox");
        sonarRepoLabel.classList.remove("wdm-disabled-label");
      } else {
        console.log("disable");
        addSonarRepoCheckbox.checked = false;
        addSonarRepoCheckbox.disabled = true;
        addSonarRepoCheckbox.classList.add("wdm-disabled-checkbox");
        sonarRepoLabel.classList.add("wdm-disabled-label");
      }

      if (projectNameValue !== "" && clientNameValue !== "") {
        addProjectCheckbox.disabled = false;
        addProjectCheckbox.classList.remove("wdm-disabled-checkbox");
        checkboxLabel.classList.remove("wdm-disabled-label");
        addGitRepoCheckbox.disabled = false;
        addGitRepoCheckbox.classList.remove("wdm-disabled-checkbox");
        gitRepoLabel.classList.remove("wdm-disabled-label");

        addSpinupRepoCheckbox.disabled = false;
        addSpinupRepoCheckbox.classList.remove("wdm-disabled-checkbox");
        spinupRepoLabel.classList.remove("wdm-disabled-label");
      } else {
        addProjectCheckbox.checked = false;

        addProjectCheckbox.disabled = true;
        addProjectCheckbox.classList.add("wdm-disabled-checkbox");
        checkboxLabel.classList.add("wdm-disabled-label");
        addGitRepoCheckbox.checked = false;
        addGitRepoCheckbox.disabled = true;
        addGitRepoCheckbox.classList.add("wdm-disabled-checkbox");
        gitRepoLabel.classList.add("wdm-disabled-label");
        addSpinupRepoCheckbox.checked = false;
        addSpinupRepoCheckbox.disabled = true;
        addSpinupRepoCheckbox.classList.add("wdm-disabled-checkbox");
        spinupRepoLabel.classList.add("wdm-disabled-label");
      }
    }

    // Add input event listeners to both project name and client name inputs
    projectNameInput.addEventListener("input", checkInputs);
    clientNameInput.addEventListener("input", checkInputs);
    gitLinkInput.addEventListener("input", checkInputs);
    gitLinkInputCheckbox.addEventListener("input", checkInputs);

    gitLinkInput.addEventListener("change", validateGitLink);

    function validateGitLink(event) {
      console.log(event.target);
    }

    // Initial check
    checkInputs();
  }

  var columns_list = [];

  var row;

  var datatable = new DataTable("#wdm-datatable", {
    initComplete: function () {
      this.api()
        .columns()
        .every(function () {
          var column = this;
          temp = column;

          columns_list.push(column);
          console.log(columns_list);
        });
    },
    layout: {
      bottomStart: {
        buttons: ["copy", "csv", "excel", "pdf", "print"],
      },
    },
    bAutoWidth: false,
    aoColumns: [
      { sWidth: "0%" },
      { sWidth: "9%" },
      { sWidth: "9%" },
      { sWidth: "9%" },
      { sWidth: "9%" },
      { sWidth: "9%" },
      { sWidth: "9%" },
      { sWidth: "9%" },
      { sWidth: "9%" },
      { sWidth: "9%" },
      { sWidth: "9%" },
      { sWidth: "10%" },
    ],
    columnDefs: [
      {
        target: 0,
        visible: false,
        searchable: false,
      },
      {
        target: 12,
        visible: false,
        searchable: false,
      },
      {
        target: 6,
        render: function (data, type, row) {
          // Render the text as a hyperlink
          return `<a href="${data}" target="_blank">${data}</a>`;
        },
        // visible: false,
        // searchable: false,
      },
      {
        target: 7,

        render: function (data, type, row) {
          // Render the text as a hyperlink
          let correctedUrl = data.replace(/—/g, "---");
          return `<a href="${correctedUrl}" target="_blank">${correctedUrl}</a>`;
        },
      },
      {
        target: 8,
        render: function (data, type, row) {
          // Render the text as a hyperlink
          return `<a href="${data}" target="_blank">${data}</a>`;
        },
      },
      {
        target: 9,
        render: function (data, type, row) {
          // Render the text as a hyperlink
          return `<a href="${data}" target="_blank">${data}</a>`;
        },
      },
    ],
  });

  // console.log(datatable.settings().init().columns)
  // Attach an event handler to dynamically monitor changes to input elements inside the `.wdm-edit-popup` container
  $(document)
    .off("input change", ".wdm-edit-popup input, .wdm-edit-popup select") // Remove previous event bindings
    .on("input", ".wdm-edit-popup input", handleInputChange) // For text inputs
    .on("change", ".wdm-edit-popup select", handleInputChange); // For select elements

  function handleInputChange() {
    // Fetch the current row's data from the datatable
    let current_row_data = datatable.row(row).data();

    // Extract the relevant part of the row's data, excluding the first and last elements
    let sliced_row = current_row_data.slice(1, current_row_data.length - 1);

    // Initialize a flag to track whether any changes were made
    let changes_made = false;

    // Define a list of mandatory fields that require specific validation
    let mandatory_fields = ["site_name", "sme_name", "developer_name"];

    // Compare the current input/select value with the corresponding value in the sliced row data
    if (this.value != sliced_row[this.getAttribute("col_num")]) {
      // If the field is mandatory and not empty, mark changes as made
      if (mandatory_fields.includes(this.name) && this.value !== "") {
        changes_made = true;
      }
      // If the field is not mandatory, consider changes as made regardless
      else if (!mandatory_fields.includes(this.name)) {
        changes_made = true;
      }

      console.log(this.value);
      console.log(sliced_row[this.getAttribute("col_num")]);
    }

    // Select the confirmation button in the popup
    let confirm_button = document.querySelector("#wdm-confirm-popup");

    if (changes_made) {
      confirm_button.disabled = false;
      confirm_button.style.backgroundColor = "green";
      confirm_button.classList.remove("disabled-button");
    } else {
      confirm_button.disabled = true;
      confirm_button.style.backgroundColor = "gray";
      confirm_button.classList.add("disabled-button");
    }
  }

  // Bind a click event handler to the "#wdm-delete-no" button
  $(document)
    .off("click", "#wdm-delete-no") // Remove any previously bound click event handlers to avoid duplication
    .on("click", "#wdm-delete-no", function () {
      // Attach a new click event handler
      // Hide the delete confirmation popup when the "No" button is clicked
      delete_popup.hide();
    });

  // Bind a click event handler to the "#wdm-delete-yes" button
  $(document)
    .off("click", "#wdm-delete-yes") // Remove any previously bound click event handlers to avoid duplication
    .on("click", "#wdm-delete-yes", function () {
      // Attach a new click event handler
      // Call the function responsible for deleting the entry when the "Yes" button is clicked
      delete_entry();
    });

  // Function to handle the deletion of an entry
  function delete_entry() {
    // Initiating an AJAX request to delete the specified entry
    jQuery.ajax({
      type: "post", // Specifies the HTTP method as POST
      url: datatable_info.ajax_url, // The server-side URL to send the request to
      data: {
        action: "delete_entry", // The action identifier for server-side handling
        nonce: datatable_info.nonce, // Security token to validate the request
        id: datatable.row(row).data()[0], // The ID of the entry to delete, fetched from the first column of the selected row
      },
      success: function (response) {
        // Callback executed when the AJAX request succeeds
        console.log(response); // Log the server response for debugging

        // Remove the corresponding row node from the DataTable
        datatable.row(row).node().remove();

        // Hide the delete confirmation popup
        delete_popup.hide();
      },
      error: function (response) {
        // Callback executed if the AJAX request fails
        // (Currently empty, but can handle error messages or logging here)
      },
    });
  }

  // Bind a click event handler to the "#wdm-confirm-popup" button
  $(document)
    .off("click", "#wdm-confirm-popup") // Remove any previously bound click event handlers to prevent duplication
    .on("click", "#wdm-confirm-popup", function () {
      // Attach a new click event handler
      // Call the `edit_entries` function when the confirm button is clicked
      edit_entries();
    });

  function edit_entries() {
    // Select all input elements within the div with the class 'wdm-popup-content'
    const inputs = document.querySelectorAll(
      ".wdm-popup-content input, .wdm-popup-content select"
    );

    // Get the ID of the current row being edited
    let row_id = datatable.row(row).data()[0];

    // Create a list to store id-value pairs
    const inputList = [];
    inputList.push({ id: "id", value: row_id }); // Add the row ID to the list

    let empty_fields = false; // Flag to track if any mandatory fields are empty

    // Iterate over the inputs to collect their id and value pairs
    inputs.forEach((input) => {
      if (input.id) {
        // Only process inputs that have an ID
        inputList.push({ id: input.id, value: input.value });
      }
    });

    // Define a list of mandatory fields
    let mandatory_fields = ["site_name", "sme_name", "developer_name"];

    // Prepare a new row data array for updating the datatable
    let new_row_data = [];
    new_row_data.push(row_id); // Add the row ID as the first column

    // Traverse the input list to check mandatory fields and prepare new row data
    inputList.forEach((item) => {
      if (item.id !== "id") {
        // Exclude the row ID from the new row's data
        new_row_data.push(item.value);
      }
      // Check if mandatory fields are empty
      if (mandatory_fields.includes(item.id) && item.value === "") {
        empty_fields = true; // Set the flag if a mandatory field is empty
      }
    });

    // If any mandatory fields are empty, alert the user and stop execution
    if (empty_fields) {
      alert("Please fill all the mandatory fields");
      return;
    }

    // Append action buttons (Edit and Delete) to the new row data

    new_row_data.push(
      '<button class="wdm-entry-edit"><i class="fas fa-edit"></i></button><button class="wdm-entry-delete"><i class="fas fa-trash-alt"></i></button>'
    );
    new_row_data.push("0");
    console.log("input list", inputList);
    // Make an AJAX request to update the entry on the server
    jQuery.ajax({
      type: "post", // Use POST method for the request
      url: datatable_info.ajax_url, // Server-side endpoint URL
      data: {
        action: "update_entry", // Action identifier for server-side handling
        nonce: datatable_info.nonce, // Security nonce for validation
        updated_data: inputList, // Updated data to send to the server
      },
      success: function (response) {
        // Update the datatable with the new row data
        datatable.row(row).data(new_row_data).draw();

        // Hide the edit popup after successful update
        edit_popup.hide();
      },
      error: function (response) {
        // Handle errors in the AJAX request (currently empty)
      },
    });
  }

  $("#wdm-datatable tbody").on("click", "button.wdm-entry-edit", function () {
    // When an edit button is clicked, execute this function

    // Disable the confirm button initially as no changes are made yet
    let confirm_button = document.querySelector("#wdm-confirm-popup");
    confirm_button.disabled = true; // Disable the button
    confirm_button.style.backgroundColor = "gray"; // Set the background color to gray
    confirm_button.classList.add("disabled-button"); // Add a CSS class for additional styling

    // Identify the table row (<tr>) containing the clicked button
    row = this.closest("tr");

    // Extract the text content of all table cells (<td>) in the row except the last one (button column)
    const data = Array.from(row.querySelectorAll("td:not(:last-child)")).map(
      (td) => td.textContent.trim() // Remove any extra whitespace from the text
    );

    // Select all input fields in the popup where the data will be populated
    const inputs = document.querySelectorAll(
      ".wdm-popup-content input, .wdm-popup-content select"
    );

    // Populate each input field with the corresponding data from the table row
    inputs.forEach((input, index) => {
      input.value = data[index] || ""; // Assign the data or set an empty string if no data exists
    });

    // Show the edit popup after populating it with the row's data
    edit_popup.show();
  });

  $("#wdm-datatable tbody").on("click", "button.wdm-entry-delete", function () {
    // Event listener for delete button clicks within the table body

    // Identify the parent row (<tr>) containing the clicked delete button
    row = this.closest("tr");

    // Show the delete confirmation popup to the user
    delete_popup.show();
  });

  $("#wdm-datatable tbody").on(
    "click",
    "button.wdm-entry-spinup-cache",
    function () {
      // Event listener for delete button clicks within the table body
      notyf.success('Starting cleaning up cache');
      // Identify the parent row (<tr>) containing the clicked delete button
      row = this.closest("tr");
      let spinup_id = datatable.row(row).data()[12];
      jQuery.ajax({
        type: "post", // Specifies the HTTP method as POST
        url: datatable_info.ajax_url, // The server-side URL to send the request to
        data: {
          action: "clear_cache", // The action identifier for server-side handling
          nonce: datatable_info.nonce, // Security token to validate the request
          id: spinup_id, // The ID of the entry to delete, fetched from the first column of the selected row
        },
        success: function (response) {
          // Callback executed when the AJAX request succeeds
          console.log(response); // Log the server response for debugging
          notyf.success('Started cleaning up cache it may take some time');
        },
        error: function (response) {
          // Callback executed if the AJAX request fails
          // (Currently empty, but can handle error messages or logging here)
        },
      });
    }
  );

  // Select all input elements within elements of class 'search-row'
  const inputsInSearchRow = document.querySelectorAll(".search-row input");

  // Iterate through each input element and attach event listeners
  inputsInSearchRow.forEach((input, index) => {
    // Add event listeners for 'keyup', 'change', and 'input' events

    // Event: 'keyup' - Triggered when the user releases a key
    input.addEventListener("keyup", function () {
      // Check if the current search value is different from the input value
      if (columns_list[index + 1].search() !== this.value) {
        // Update the column's search with the input value and redraw the table
        columns_list[index + 1].search(this.value).draw();
      }
    });

    // Event: 'change' - Triggered when the input loses focus after a value change
    input.addEventListener("change", function () {
      // Check if the current search value is different from the input value
      if (columns_list[index + 1].search() !== this.value) {
        // Update the column's search with the input value and redraw the table
        columns_list[index + 1].search(this.value).draw();
      }
    });

    // Event: 'input' - Triggered for every change to the input value, including clearing
    input.addEventListener("input", function () {
      // Check if the current search value is different from the input value
      if (columns_list[index + 1].search() !== this.value) {
        // Update the column's search with the input value and redraw the table
        columns_list[index + 1].search(this.value).draw();
      }
    });
  });

  // Attach event listeners to the element with the ID 'search-box'
  $("#search-box").on("keyup change clear", function () {
    // Check if the current search value in 'temp' is different from the input box value
    if (temp.search() !== this.value) {
      // Update the search term in 'temp' with the new value from the input box
      temp.search(this.value).draw(); // Redraw the DataTable to reflect the new search results
    }
  });

  // Select the first element with the class 'search-row' from the document
  const search_row = document.querySelector(".search-row");

  // Check if the 'search_row' element exists in the document
  if (search_row) {
    // Add an event listener to the 'search_row' element for the 'click' event
    search_row.addEventListener("click", (event) => {
      // Prevent the click event from propagating further up the DOM tree
      event.stopImmediatePropagation();
    });
  }

  const edit_popup = new Popup({
    id: "wdm-edit-popup",
    title: "Edit details",
    content: `<div class="wdm-popup-content">

        <div class="wdm-input-row">
          <div class="wdm-input-group">
            <label for="site_name">Site Name</label>
            <input type="text" id="site_name" name="site_name" col_num="0" required>
          </div>
          <div class="wdm-input-group">
            <label for="sme_name">SME Name</label>
            <select id="sme_name" class="wdm_team_edit_dropdown" name="sme_name" col_num="1" required>
              <option value="Shamali">Shamali</option>
              <option value="Shruti">Shruti</option>
              <option value="Akshay">Akshay</option>
              <option value="Nikhil">Nikhil</option>
              <option value="Foram">Foram</option>
            </select>
          </div>
          
        </div>
        <div class="wdm-input-row">
          <div class="wdm-input-group">
            <label for="developer_name">Developer Name</label>
            <input type="text" id="developer_name" name="developer_name" col_num="2" required>
          </div>
          <div class="wdm-input-group">
            <label for="client_name">Client Name</label>
            <input type="text" id="client_name" name="client_name" col_num="3" required>
          </div>
        </div>
        <div class="wdm-input-row">
          <div class="wdm-input-group">
            <label for="project_name">Project Name</label>
            <input type="text" id="project_name" name="project_name" col_num="4" required>
          </div>
          <div class="wdm-input-group">
            <label for="tracking_time_link">Tracking Time Link</label>
            <input type="text" id="tracking_time_link" name="tracking_time_link" col_num="5" required>
          </div>
        </div>
        <div class="wdm-input-row">
          <div class="wdm-input-group">
            <label for="git_link">Git Link</label>
            <input type="text" id="git_link" name="git_link" col_num="6" required>
          </div>
          <div class="wdm-input-group">
            <label for="sonar_link">Sonar Link</label>
            <input type="text" id="sonar_link" name="sonar_link" col_num="7" required>
          </div>
        </div>
        <div class="wdm-input-row">
          <div class="wdm-input-group">
            <label for="spinup_link">Sonar Link</label>
            <input type="text" id="spinup_link" name="spinup_link" col_num="8" required>
          </div>
          <div class="wdm-input-group">
            <label for="team_name">Team Name</label>
            <select id="team_name" class="wdm_team_edit_dropdown" name="team_name" col_num="9" required>
              <option value="Orion">Orion</option>
              <option value="Phoenix">Phoenix</option>
              <option value="Cygnus">Cygnus</option>
              <option value="Volans">Volans</option>
              <option value="Techops">Techops</option>
            </select>
          </div>
        </div>
        <div class="wdm-popup-buttons">
          <button type="submit" id="wdm-confirm-popup">Confirm</button>
        </div>

    </div>
			`,
  });
  const delete_popup = new Popup({
    id: "wdm-delete-popup",
    title: "Are you sure",
    content: `<div class = "wdm-delete-popup-buttons">
      <button id="wdm-delete-yes">Yes</button>
      <button id="wdm-delete-no">No</button>
    </div>
			`,
  });

  const submitButton = document.querySelector(".wdm-submit-btn");

  // Check if the button exists to avoid errors
  if (submitButton) {
    // Add a click event listener to the button
    submitButton.addEventListener("click", () => {
      // Show loader
      submitButton.innerHTML =
        '<i class="fas fa-spinner fa-spin"></i> Submitting...';
      submitButton.disabled = true;

      if(addSpinupRepoCheckbox.checked){
        let domain_name = document.getElementById("wdm_spinup_domain") ? document.getElementById("wdm_spinup_domain").value : "";
        if (!domain_name || domain_name.includes(" ") || domain_name.includes("_") ) {
          swal("Error", "Please provide a valid domain name (not containing spaces or underscores)", "error");
          submitButton.innerHTML = "Submit";
          submitButton.disabled = false;
          return; // Exit the function if a mandatory field is empty
        } 
      }

      // Perform the desired action upon button click
      const targetDiv = document.querySelector(".wdm-site-details-form"); // Replace with the actual class name of the form

      if (targetDiv) {
        // Find all input elements within the form container
        const inputs = targetDiv.querySelectorAll("input, select");
        const form_inputs = {};
        let new_row_data = [];

        // Iterate over the input elements to gather their values
        for (let i = 0; i < inputs.length; i++) {
          const input = inputs[i];
          console.log(input.value);
          console.log(input.getAttribute("ignore"));

          // Check if the first 3 mandatory fields are empty
          if (i < 3 && input.value === "") {
            swal("Error", "Please fill all the mandatory fields", "error");
            submitButton.innerHTML = "Submit";
            submitButton.disabled = false;
            return; // Exit the function if a mandatory field is empty
          }

          if (
            input.id &&
            ![
              "wdm_add_project_tracking_time",
              "wdm_add_git_repo",
              "wdm_add_sonar",
              "wdm_add_spinup",
            ].includes(input.id)
          ) {
            if (!input.getAttribute("ignore")) {
              new_row_data.push(input.value);
            }

            form_inputs[input.id] = input.value;
          }
        }

        // Add edit and delete buttons to new row data
        
        

        form_inputs["wdm_add_project_tracking_time"] =
          addProjectCheckbox.checked;
        form_inputs["wdm_add_git_repo"] = addGitRepoCheckbox.checked;
        form_inputs["wdm_add_sonar"] = addSonarRepoCheckbox.checked;
        form_inputs["wdm_add_spinup"] = addSpinupRepoCheckbox.checked;

        console.log("Input Data:", form_inputs);

        // Perform AJAX request
        jQuery.ajax({
          type: "post",
          url: datatable_info.ajax_url,
          data: {
            action: "add_entry",
            nonce: datatable_info.nonce,
            new_entry: form_inputs,
          },
          success: function (response) {
            // Hide loader
            submitButton.innerHTML = "Submit";
            submitButton.disabled = false;

            // Clear input fields
            inputs.forEach((input) => {
              if (input.type != "select-one") {
                input.value = "";
              }
              
              
            });

            if (response.success) {
              let message = `<p>Details stored successfully.</p>`;

              if (response.data.tracking_time_link) {
                message += `<p>
                              Tracking time link:
                              <a href="${response.data.tracking_time_link}" target="_blank">
                                Link
                              </a>
                              <i onclick="copyLink(this)" class="fa-solid fa-copy wdm-copy-button" data-link="${response.data.tracking_time_link}"></i>

                            </p>`;
              }
              if (response.data.git_repo_url) {
                message += `<p>
                              Git repository link:
                              <a href="${response.data.git_repo_url}" target="_blank">
                                Link
                              </a>
                              <i onclick="copyLink(this)" class="fa-solid fa-copy wdm-copy-button" data-link="${response.data.git_repo_url}"></i>

                            </p>`;
              }
              if (response.data.sonar_url) {
                message += `<p>
                              SonarQube link:
                              <a href="${response.data.sonar_url}" target="_blank">
                                Link
                              </a>
        <i onclick="copyLink(this)" class="fa-solid fa-copy wdm-copy-button" data-link="${response.data.sonar_url}"></i>
                            
                            </p>`;
              }
              if (response.data.spinup_url) {
                message += `<p>
                              Spinup link:
                              <a href="${response.data.spinup_url}" target="_blank">
                                Link
                              </a>
        <i onclick="copyLink(this)" class="fa-solid fa-copy wdm-copy-button" data-link="${response.data.spinup_url}"></i>
                            
                            </p>`;
              }

              Swal.fire({
                title:
                  response.data.tt_message || response.data.git_repo_message
                    ? "Warning"
                    : "Success",
                html: message,
                icon:
                  response.data.tt_message || response.data.git_repo_message
                    ? "warning"
                    : "success",
              });

              addProjectCheckbox.checked = false;
              addGitRepoCheckbox.checked = false;
              addSonarRepoCheckbox.checked = false;
              addSpinupRepoCheckbox.checked = false;

              addProjectCheckbox.disabled = true;
              addGitRepoCheckbox.disabled = true;
              addSonarRepoCheckbox.disabled = true;
              addSpinupRepoCheckbox.disabled = true;

              trackingTimeLinkInput.classList.remove("wdm-disabled-input");
              gitLinkInput.classList.remove("wdm-disabled-input");
              sonarLinkInput.classList.remove("wdm-disabled-input");
              spinupLinkInput.classList.remove("wdm-disabled-input");

              
  
              addGitRepoCheckbox.classList.add("wdm-disabled-checkbox");
              gitRepoLabel.classList.add("wdm-disabled-label");
              addProjectCheckbox.classList.add("wdm-disabled-checkbox");
              checkboxLabel.classList.add("wdm-disabled-label");
              addSonarRepoCheckbox.classList.add("wdm-disabled-checkbox");
              sonarRepoLabel.classList.add("wdm-disabled-label");
              addSpinupRepoCheckbox.classList.add("wdm-disabled-checkbox");
              spinupRepoLabel.classList.add("wdm-disabled-label");

              console.log('daga')
              let spinupSettingsContainer = document.querySelector(".wdm-spinup-settings-container");
              spinupSettingsContainer.style.display === "none"
              spinupSettingsContainer.classList.add("hidden");
              setTimeout(() => {
                spinupSettingsContainer.style.display = "none";
              }, 500); // Matches transition duration

              // Populate new row data
              new_row_data.unshift(response.data.new_entry_id);
              new_row_data[6] = response.data.tracking_time_link || "";
              new_row_data[7] = response.data.git_repo_url || "";
              new_row_data[8] = response.data.sonar_url || "";
              new_row_data[9] = response.data.spinup_url || "";
              let last_row_button = '<button class="wdm-entry-edit"><i class="fas fa-edit"></i></button><button class="wdm-entry-delete"><i class="fas fa-trash-alt"></i></button>'
              
              if (response.data.spinup_id) {
                last_row_button += '<button class="wdm-entry-spinup-cache"><i class="fas fa-sync"></i></button>';
                new_row_data.push(last_row_button);
                new_row_data.push(response.data.spinup_id);
              }
              else{
                new_row_data.push(last_row_button);
                new_row_data.push("");
              }
              
              console.log(new_row_data);

              datatable.row.add(new_row_data).draw(false);
            } else {
              swal("Error", response.data.message, "error");
            }
          },
          error: function (response) {
            // Hide loader
            console.log(response);
            submitButton.innerHTML = "Submit";
            submitButton.disabled = false;

            // Handle errors
            swal(
              "Warning",
              "An error occurred while adding the entry.",
              "warning"
            );
          },
        });
      }
    });
  }
});
