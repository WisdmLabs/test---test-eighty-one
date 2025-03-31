(function ($) {
  "use strict";
  function validateSpinupToken(tokenValue) {
    return new Promise((resolve, reject) => {
        jQuery.ajax({
            type: "post",
            url: tracking_time_settings?.ajax_url,
            data: {
                action: "validate_spinup_token",
                nonce: tracking_time_settings?.nonce,
                token: tokenValue,
            },
            success: function (response) {
                resolve(response.success);
            },
            error: function (response) {
                resolve(false);
            },
        });
    });
}

  /**
   * Validate the token by making a POST request to the Tracking Time API.
   *
   * @param {string} tokenValue - The token value to validate.
   * @returns {Promise<boolean>} - Returns a promise that resolves to true if the token is valid, otherwise false.
   */
  function validateToken(tokenValue) {
    const url = "https://app.trackingtime.co/api/v4/tasks";

    return fetch(url, {
      method: "POST",
      headers: {
        Authorization: `Basic ${tokenValue}`,
        "Content-Type": "application/json",
      },
    })
      .then((response) => {
        if (!response.ok) {
          throw new Error("Network response was not ok " + response.statusText);
        }
        return response.json();
      })
      .then((data) => {
        return true;
      })
      .catch((error) => {
        return false;
      });
  }

  function validateGitHubToken(tokenValue) {
    const url = "https://api.github.com/user";

    return fetch(url, {
      method: "GET",
      headers: {
        Authorization: `token ${tokenValue}`,
        "Content-Type": "application/json",
      },
    })
      .then((response) => {
        if (!response.ok) {
          throw new Error("Network response was not ok " + response.statusText);
        }
        return response.json();
      })
      .then((data) => {
        return true;
      })
      .catch((error) => {
        return false;
      });
  }

  function validateSonar(sonarToken) {
    return new Promise((resolve, reject) => {
      jQuery.ajax({
        type: "post",
        url: tracking_time_settings?.ajax_url,
        data: {
          action: "validate_sonar_token",
          nonce: tracking_time_settings?.nonce,
          token: sonarToken,
        },
        success: function (response) {
          resolve(response.data);
        },
        error: function (response) {
          resolve(false);
        },
      });
    });
  }

  /**
   * Initialize the document and set up event listeners.
   */
  document.addEventListener("DOMContentLoaded", function () {
    const saveButton = document.querySelector(".wdm-tt-button");
    if (!saveButton) {
        console.error("Save button not found.");
        return;
    }

    let ttToken = tracking_time_settings?.ttToken || "";
    const tokenInput = document.getElementById("wdm-tt-bearer-token");
    if (tokenInput) {
        tokenInput.value = ttToken;
    } else {
        console.error("Token input field not found.");
        return;
    }

    let gitToken = tracking_time_settings?.gitToken || "";
    const gitTokenInput = document.getElementById("wdm-git-bearer-token");
    if (gitTokenInput) {
        gitTokenInput.value = gitToken;
    } else {
        console.error("GitHub Token input field not found.");
        return;
    }

    let sonarToken = tracking_time_settings?.sonarToken || "";
    const sonarTokenInput = document.getElementById("wdm-sonar-bearer-token");
    if (sonarTokenInput) {
        sonarTokenInput.value = sonarToken;
    } else {
        console.error("Sonar Token input field not found.");
        return;
    }

    let spinupToken = tracking_time_settings?.spinupToken || "";
    const spinupTokenInput = document.getElementById("wdm-spinup-bearer-token");
    if (spinupTokenInput) {
        spinupTokenInput.value = spinupToken;
    } else {
        console.error("Spinup Token input field not found.");
        return;
    }

    function toggleSaveButton() {
        const ttTokenValue = tokenInput.value.trim();
        const gitTokenValue = gitTokenInput.value.trim();
        const sonarTokenValue = sonarTokenInput.value.trim();
        const spinupTokenValue = spinupTokenInput.value.trim();
        saveButton.disabled = !ttTokenValue && !gitTokenValue && !sonarTokenValue && !spinupTokenValue;
    }

    toggleSaveButton();

    tokenInput.addEventListener("input", toggleSaveButton);
    gitTokenInput.addEventListener("input", toggleSaveButton);
    sonarTokenInput.addEventListener("input", toggleSaveButton);
    spinupTokenInput.addEventListener("input", toggleSaveButton);

    saveButton.addEventListener("click", function () {
        ttToken = tokenInput.value;
        gitToken = gitTokenInput.value;
        sonarToken = sonarTokenInput.value;
        spinupToken = spinupTokenInput.value;

        Swal.fire({
            title: "Validating tokens...",
            text: "",
            allowOutsideClick: false,
            showConfirmButton: false,
            willOpen: () => {
                Swal.showLoading();
            },
        });

        let ttTokenValid = ttToken ? validateToken(ttToken) : Promise.resolve(true);
        let gitTokenValid = gitToken ? validateGitHubToken(gitToken) : Promise.resolve(true);
        let sonarTokenValid = sonarToken ? validateSonar(sonarToken) : Promise.resolve(true);
        let spinupTokenValid = spinupToken ? validateSpinupToken(spinupToken) : Promise.resolve(true);

        Promise.all([ttTokenValid, gitTokenValid, sonarTokenValid, spinupTokenValid])
        .then((results) => {
            const [ttValid, gitValid, sonarValid, spinupValid] = results;

            Swal.close();

            if (ttValid && gitValid && sonarValid && spinupValid) {
                Swal.fire({
                    icon: "success",
                    title: "Success!",
                    text: "Tokens stored successfully.",
                });
            } else {
                let errorMessage = "One or more tokens are invalid!";
                if (!ttValid) errorMessage += "\nTracking Time Token is invalid.";
                if (!gitValid) errorMessage += "\nGitHub Token is invalid.";
                if (!sonarValid) errorMessage += "\nSonar Token is invalid.";
                if (!spinupValid) errorMessage += "\nSpinup Token is invalid.";

                Swal.fire({
                    icon: "error",
                    title: "Oops...",
                    text: errorMessage,
                });
            }

            if (ttValid && ttToken) {
                jQuery.ajax({
                    type: "post",
                    url: tracking_time_settings?.ajax_url,
                    data: {
                        action: "save_token",
                        nonce: tracking_time_settings?.nonce,
                        token: ttToken,
                    },
                    success: function (response) {
                        // Callback executed when the AJAX request succeeds
                    },
                    error: function (response) {
                        console.error("AJAX request failed:", response);
                    },
                });
            }

            if (gitValid && gitToken) {
                jQuery.ajax({
                    type: "post",
                    url: tracking_time_settings?.ajax_url,
                    data: {
                        action: "save_git_token",
                        nonce: tracking_time_settings?.nonce,
                        token: gitToken,
                    },
                    success: function (response) {
                        // Callback executed when the AJAX request succeeds
                    },
                    error: function (response) {
                        console.error("AJAX request failed:", response);
                    },
                });
            }

            if (sonarValid && sonarToken) {
                jQuery.ajax({
                    type: "post",
                    url: tracking_time_settings?.ajax_url,
                    data: {
                        action: "save_sonar_token",
                        nonce: tracking_time_settings?.nonce,
                        token: sonarToken,
                    },
                    success: function (response) {
                        // Callback executed when the AJAX request succeeds
                    },
                    error: function (response) {
                        console.error("AJAX request failed:", response);
                    },
                });
            }

            if (spinupValid && spinupToken) {
                jQuery.ajax({
                    type: "post",
                    url: tracking_time_settings?.ajax_url,
                    data: {
                        action: "save_spinup_token",
                        nonce: tracking_time_settings?.nonce,
                        token: spinupToken,
                    },
                    success: function (response) {
                        // Callback executed when the AJAX request succeeds
                    },
                    error: function (response) {
                        console.error("AJAX request failed:", response);
                    },
                });
            }
        })
        .catch((error) => {
            Swal.close();
            Swal.fire({
                icon: "error",
                title: "Oops...",
                text: "An error occurred while validating the tokens.",
            });
            console.error("Token validation failed:", error);
        });
    });
});

})(jQuery);
