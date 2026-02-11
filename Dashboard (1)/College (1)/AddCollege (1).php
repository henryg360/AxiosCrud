<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add College</title>
    <link rel="stylesheet" href="../../Assets/AddCollege.css">
    <script src="../../axios/axios.min.js"></script>
    <script src="AddCollege.js" defer></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <div class="form-container">
        <form id="collegeForm">
            <table>
                <tr>
                    <th colspan="2">Add College Data</th>
                </tr>

                <tr>
                    <td>College ID</td>
                    <td><input type="text" name="college_id"></td>
                </tr>
                <tr>
                    <td>Full Name</td>
                    <td><input type="text" name="full_name"></td>
                </tr>
                <tr>
                    <td>Short Name</td>
                    <td><input type="text" name="short_name"></td>
                </tr>
                <tr>
                    <td colspan="2" class="button-row">
                        <button type="submit" id="savebtn">Save</button>
                        <input type="reset" value="Clear Entries" id="clearbtn">
                        <button type="button" id="cancelbtn" onclick="window.location.href='CollegeListings.php'">Cancel</button>
                    </td>
                </tr>
            </table>
        </form>
    </div>

    <!-- Error Modal -->
    <div class="modal fade" id="errorModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <ul id="modalErrorList" style="list-style: none; padding: 0;"></ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <p id="successMessage">College added successfully!</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="successOkBtn">OK</button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
