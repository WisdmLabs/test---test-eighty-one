(function ($) {
    "use strict";

    /**
     * Fetch stored teams from the localized data and populate the table.
     */
    function loadTeams() {
        const storedTeams = team_settings.storedTeams || [];
        const teamTableBody = document.querySelector("#wdm-team-table tbody");
    
        storedTeams.forEach(team => {
            const newRow = document.createElement("tr");
            newRow.innerHTML = `
                <td><input type="text" class="wdm-team-name" value="${team.name}" placeholder="Team Name"></td>
                <td><input type="text" class="wdm-team-id" value="${team.id}" placeholder="Team ID"></td>
                <td><input type="text" class="wdm-team-leader" value="${team.leader}" placeholder="Team Leader"></td>
                <td><input type="text" class="wdm-team-lead-name" value="${team.leadName}" placeholder="Team Lead Name"></td> <!-- New column -->
                <td><button class="wdm-remove-team-button">Remove</button></td>
            `;
            teamTableBody.appendChild(newRow);
    
            const removeButton = newRow.querySelector(".wdm-remove-team-button");
            removeButton.addEventListener("click", function () {
                newRow.remove();
            });
        });
    }
    

    document.addEventListener("DOMContentLoaded", function () {
        const addTeamButton = document.getElementById("wdm-add-team-button");
        const teamTableBody = document.querySelector("#wdm-team-table tbody");
        if(teamTableBody) {
            
        
        if (addTeamButton) {
            addTeamButton.addEventListener("click", function () {
                const newRow = document.createElement("tr");
                newRow.innerHTML = `
                    <td><input type="text" class="wdm-team-name" placeholder="Team Name"></td>
                    <td><input type="text" class="wdm-team-id" placeholder="Team ID"></td>
                    <td><input type="text" class="wdm-team-leader" placeholder="Team Leader"></td>
                    <td><input type="text" class="wdm-team-lead-name" placeholder="Team Lead Name"></td> <!-- New column -->
                    <td><button class="wdm-remove-team-button">Remove</button></td>
                `;
                teamTableBody.appendChild(newRow);
            
                const removeButton = newRow.querySelector(".wdm-remove-team-button");
                removeButton.addEventListener("click", function () {
                    newRow.remove();
                });
            });            
        }

        const saveTeamButton = document.createElement("button");
        saveTeamButton.textContent = "Save Teams";
        saveTeamButton.classList.add("wdm-save-teams-button");
        document.querySelector(".wdm-team-container").appendChild(saveTeamButton);

        saveTeamButton.addEventListener("click", function () {
            const teams = [];
            let isValid = true;
        
            document.querySelectorAll("#wdm-team-table tbody tr").forEach(row => {
                const teamName = row.querySelector(".wdm-team-name").value.trim();
                const teamId = row.querySelector(".wdm-team-id").value.trim();
                const teamLeader = row.querySelector(".wdm-team-leader").value.trim();
                const teamLeadName = row.querySelector(".wdm-team-lead-name").value.trim(); // New field
        
                if (teamName && teamLeader && teamLeadName) {
                    teams.push({ name: teamName, id: teamId, leader: teamLeader, leadName: teamLeadName }); // Include new field
                } else if (teamName || teamLeader || teamLeadName) {
                    isValid = false;
                }
            });
        
            if (!isValid) {
                Swal.fire({
                    icon: "error",
                    title: "Oops...",
                    text: "Please fill in all fields for each team.",
                });
                return;
            }
        
            jQuery.ajax({
                type: "post",
                url: team_settings.ajax_url,
                data: {
                    action: "save_team_data",
                    nonce: team_settings.nonce,
                    teams: teams,
                },
                success: function (response) {
                    if (response.success) {
                        Swal.fire({
                            icon: "success",
                            title: "Success!",
                            text: "Team settings saved successfully.",
                        });
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Oops...",
                            text: "Failed to save team settings.",
                        });
                    }
                },
                error: function (response) {
                    Swal.fire({
                        icon: "error",
                        title: "Oops...",
                        text: "An error occurred while saving team settings.",
                    });
                },
            });
        });
        

        // Load stored teams when the page loads
        loadTeams();
    }
    });
})(jQuery);
