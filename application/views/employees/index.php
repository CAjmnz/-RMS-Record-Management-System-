<!DOCTYPE html>
<html>
<head>
    <title><?php echo $title; ?></title>

    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

    <style>
        body {
            background: #f0f2f5;
        }

        .card-custom {
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }

        .table thead {
            background: #343a40;
            color: #fff;
        }
    </style>
</head>
<body>

<?php $this->load->view('templates/navbar'); ?>

<div class="container mt-5">

    <div class="card card-custom">

        <div class="card-header d-flex justify-content-between">

            <h4 class="mb-0">Employee Directory</h4>

            <input type="text"
                   id="searchInput"
                   class="form-control w-25"
                   placeholder="Search employee...">

        </div>

        <div class="card-body table-responsive">

            <table class="table table-bordered table-hover" id="employeeTable">

                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Department</th>
                        <th>Job Title</th>
                        <th>Email</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>

                <?php foreach ($employees as $emp): ?>
                    <tr>
                        <td><?php echo $emp->id; ?></td>

                        <td>
                            <?php echo $emp->firstname . ' ' . $emp->lastname; ?>
                        </td>

                        <td><?php echo $emp->department ?? 'N/A'; ?></td>

                        <td><?php echo $emp->job_title ?? 'N/A'; ?></td>

                        <td><?php echo $emp->email; ?></td>

                        <td>
                            <span class="badge badge-<?php echo $emp->is_active ? 'success' : 'secondary'; ?>">
                                <?php echo $emp->is_active ? 'Active' : 'Inactive'; ?>
                            </span>
                        </td>

                    </tr>
                <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function(){

    $("#searchInput").on("keyup", function() {
        var value = $(this).val().toLowerCase();

        $("#employeeTable tbody tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });

});
</script>

</body>
</html>