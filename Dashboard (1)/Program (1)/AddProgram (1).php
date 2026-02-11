<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Program Entry</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../Assets/AddPrograms.css">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="../../axios/axios.min.js"></script>
    <script src="AddProgram.js" defer></script>
</head>
<body>
    <div class="form-container">
        <form id="programForm">
            <table>
                <tr>
                    <th colspan="2">Program Information Data Entry</th>
                </tr>
                <tr>
                    <td>Program ID</td>
                    <td><input type="text" name="program_id"></td>
                </tr>
                <tr>
                    <td>Program Name</td>
                    <td><input type="text" name="program_name"></td>
                </tr>
                <tr>
                    <td>Program Short Name</td>
                    <td><input type="text" name="program_shortname"></td>
                </tr>
                <tr>
                    <td>College</td>
                    <td>
                        <select name="college_id">
                            <option value="">Select College</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Department</td>
                    <td>
                        <select name="department_id">
                            <option value="">Select Department</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" class="button-row">
                        <button type="submit" id="savebtn">Save</button>
                        <input type="reset" value="Clear Entries" id="clearbtn">
                        <button type="button" id="cancelbtn" onclick="window.location.href='ProgramListing.php'">Cancel</button>
                    </td>
                </tr>
            </table>
        </form>
    </div>

    <!-- Modal for displaying messages -->
    <div class="modal fade" id="messageModal" tabindex="-1" role="dialog" aria-labelledby="messageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="messageModalLabel"></h5>
                </div>
                <div class="modal-body">
                    <ul id="messageList"></ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
