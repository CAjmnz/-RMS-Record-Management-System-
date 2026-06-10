$(function () {
	/* ═══════════════════════════════════════════════════════
   AUTO-GENERATE EMPLOYEE ID ON CREATE MODAL OPEN
═══════════════════════════════════════════════════════ */
	$("#createUserModal").on("shown.bs.modal", function () {
		$.ajax({
			url: BASE_URL + "users/generate_employee_id",
			type: "GET",
			dataType: "json",
			success: function (res) {
				if (res.success && res.data.employee_id) {
					// target ONLY create modal input
					$("#createUserModal #employee_id").val(res.data.employee_id);
				}
			},
			error: function () {
				showToast("danger", "Failed to generate Employee ID.");
			},
		});
	});

	/* ══════════════════════════════════════════════════════════════════
       CSRF
       ══════════════════════════════════════════════════════════════════ */
	function csrfData() {
		return {
			[$("#csrf_token_name").val()]: $("#csrf_token_value").val(),
		};
	}

	/* ══════════════════════════════════════════════════════════════════
       CONTACT NUMBER — block non-numeric input at the JS layer
       Strips anything that isn't a digit as the user types.
       Backend validation is still the final authority.
       ══════════════════════════════════════════════════════════════════ */
	$(document).on("input", "#contactno, #edit_contactno", function () {
		// Remove any non-digit character instantly
		var cleaned = $(this).val().replace(/\D/g, "");
		// Enforce max 11 digits
		if (cleaned.length > 11) cleaned = cleaned.slice(0, 11);
		$(this).val(cleaned);
	});

	// Block non-numeric keypresses (arrows, backspace, delete, tab still allowed)
	$(document).on("keypress", "#contactno, #edit_contactno", function (e) {
		var char = String.fromCharCode(e.which);
		if (!/[0-9]/.test(char)) {
			e.preventDefault();
		}
	});

	/* ══════════════════════════════════════════════════════════════════
       DATATABLES — server-side, role-aware columns
       ══════════════════════════════════════════════════════════════════ */

	// RMS_ROLE is set in the view: <script>var RMS_ROLE = "<?= $role ?>";</script>
	var isAdmin = typeof RMS_ROLE !== "undefined" && RMS_ROLE === "admin";

	// Base columns — same for all roles
	var columns = [
		// col 0 — User: avatar + name + email merged into one cell
		{
			data: null,
			orderable: true,
			render: function (row) {
				var avatar = row.profile_picture
					? row.profile_picture
					: '<div class="profile-initials-sm">' +
						(row.firstname ? row.firstname.charAt(0).toUpperCase() : "?") +
						"</div>";
				return (
					'<div style="display:flex;align-items:center;gap:10px;">' +
					avatar +
					row.user +
					"</div>"
				);
			},
		},
		{ data: "role", orderable: true },
		{ data: "status", orderable: true },
		{ data: "contact", orderable: false },
		{ data: "address", orderable: false },
		{ data: "created", orderable: true },
		{ data: "department", orderable: true },
		{ data: "birthday", orderable: true },
	];

	// Actions column only for admins — MUST match the <th> rendered in index.php
	if (isAdmin) {
		columns.push({ data: "actions", orderable: false });
	}

	var table = $("#usersTable").DataTable({
		processing: true,
		serverSide: true,
		ajax: {
			url: BASE_URL + "users/ajax_list",
			type: "POST",
			data: function (d) {
				d.role = $("#filterRole").val() || "";
				d.status = $("#filterStatus").val() || "";
				d.date = $("#filterDate").val() || "";
				d.dept = $("#filterDepartment").val() || "";
			},
		},
		columns: columns,
		columnDefs: [{ targets: -1, orderable: false }],
		order: [[5, "desc"]],
		pageLength: 10,
		lengthMenu: [5, 10, 25, 50],
		language: {
			processing:
				'<div class="text-center py-3"><div class="spinner-border text-primary" role="status"></div></div>',
			emptyTable: "No users found.",
			zeroRecords: "No matching users found.",
		},
		drawCallback: function () {
			$(".rms-dropdown-menu.show").removeClass("show");
		},
	});

	/* ══════════════════════════════════════════════════════════════════
       CUSTOM DROPDOWN (works in dynamically rendered DataTables rows)
       ══════════════════════════════════════════════════════════════════ */
	$(document).on("click", ".rms-dropdown-toggle", function (e) {
		e.stopPropagation();
		var $menu = $(this).siblings(".rms-dropdown-menu");
		$(".rms-dropdown-menu.show").not($menu).removeClass("show");
		$menu.toggleClass("show");
	});

	$(document).on("click", function () {
		$(".rms-dropdown-menu.show").removeClass("show");
	});

	/* ══════════════════════════════════════════════════════════════════
       COLUMN FILTERS
       ══════════════════════════════════════════════════════════════════ */
	$("#filterRole, #filterStatus, #filterDate, #filterDepartment").on(
		"change input",
		function () {
			table.ajax.reload();
		},
	);

	$("#resetFilters").on("click", function () {
		$("#filterRole").val("");
		$("#filterStatus").val("");
		$("#filterDate").val("");
		$("#filterDepartment").val("");
		table.ajax.reload();
	});

	/* ══════════════════════════════════════════════════════════════════
       VALIDATION UI HELPERS
       ══════════════════════════════════════════════════════════════════ */
	var MAX_VISIBLE = 3;

	function setFieldError($input, message) {
		$input.removeClass("is-valid").addClass("is-invalid");
		var $wrap = $input.closest(".field-wrap");
		$wrap.addClass("has-error");
		$wrap.find(".error-tooltip").text(message);
	}

	function setFieldValid($input) {
		$input.removeClass("is-invalid").addClass("is-valid");
		var $wrap = $input.closest(".field-wrap");
		$wrap.removeClass("has-error");
		$wrap.find(".error-tooltip").text("");
	}

	function clearAllErrors(modalId) {
		var $modal = $(modalId);
		$modal.find(".form-control").removeClass("is-invalid is-valid");
		$modal.find(".field-wrap").removeClass("has-error");
		$modal.find(".error-tooltip").text("");
		var alertId =
			modalId === "#createUserModal" ? "#createAlert" : "#editAlert";
		$(alertId).html("").hide();
	}

	function applyErrors(modalId, alertId, errors, fieldMap) {
		$.each(fieldMap, function (name, $input) {
			if (errors[name]) {
				setFieldError($input, errors[name]);
			} else if ($input.val() !== "") {
				setFieldValid($input);
			}
		});

		var items = [];
		$.each(errors, function (name, msg) {
			items.push(labelFor(name) + " — " + msg.replace(/\.$/, ""));
		});

		if (items.length === 0) return;

		var uid = alertId.replace("#", "");
		var html = '<div style="font-size:.82rem;line-height:1.5;">';
		html +=
			'<strong style="display:block;margin-bottom:4px;">&#9888; Please fix the following:</strong>';
		html += '<ul class="mb-0 pl-3" id="errList_' + uid + '">';
		items.forEach(function (text, idx) {
			var hidden = idx >= MAX_VISIBLE ? ' class="err-extra d-none"' : "";
			html += "<li" + hidden + ">" + $("<span>").text(text).html() + "</li>";
		});
		html += "</ul>";

		if (items.length > MAX_VISIBLE) {
			var remaining = items.length - MAX_VISIBLE;
			html +=
				'<button type="button" class="btn-see-more" data-expanded="0">' +
				"See More (" +
				remaining +
				" more)</button>";
		}
		html += "</div>";

		$(alertId)
			.html(html)
			.removeClass()
			.addClass("alert alert-danger p-2 mt-1 mb-2")
			.show();

		$(alertId)
			.find(".btn-see-more")
			.on("click", function () {
				var $btn = $(this);
				var expanded =
					$btn.data("expanded") === 1 || $btn.data("expanded") === "1";
				var rem = items.length - MAX_VISIBLE;
				if (expanded) {
					$(alertId).find(".err-extra").addClass("d-none");
					$btn.text("See More (" + rem + " more)").data("expanded", 0);
				} else {
					$(alertId).find(".err-extra").removeClass("d-none");
					$btn.text("See Less").data("expanded", 1);
				}
			});
	}

	function labelFor(name) {
		var map = {
			firstname: "First Name",
			lastname: "Last Name",
			employee_id: "Employee ID",
			birthday: "Birthday",
			contactno: "Contact No",
			address: "Address",
			email: "Email",
			password: "Password",
			role: "Role",
			is_active: "Status",
			job_title: "Job Title",
			department: "Department",
		};
		return map[name] || name.charAt(0).toUpperCase() + name.slice(1);
	}

	/* ══════════════════════════════════════════════════════════════════
       REAL-TIME FIELD FEEDBACK
       ══════════════════════════════════════════════════════════════════ */
	$("#createUserModal, #editUserModal").on(
		"input change",
		".form-control",
		function () {
			var $el = $(this);
			if ($el.hasClass("is-invalid") && $.trim($el.val()) !== "") {
				setFieldValid($el);
			}
		},
	);

	/* ══════════════════════════════════════════════════════════════════
       RESET ON MODAL CLOSE
       ══════════════════════════════════════════════════════════════════ */
	$("#createUserModal").on("hidden.bs.modal", function () {
		$(this).find(".form-control").val("");
		$("#password").val("rms-2026");
		$("#create_role").val("user");
		$("#is_active").val("1");
		clearAllErrors("#createUserModal");
	});

	$("#editUserModal").on("hidden.bs.modal", function () {
		clearAllErrors("#editUserModal");
	});

	/* ══════════════════════════════════════════════════════════════════
       FIELD MAPS
       ══════════════════════════════════════════════════════════════════ */
	function createFieldMap() {
		return {
			firstname: $("#firstname"),
			lastname: $("#lastname"),
			employee_id: $("#employee_id"),
			birthday: $("#birthday"),
			contactno: $("#contactno"),
			address: $("#address"),
			email: $("#email"),
			password: $("#password"),
			role: $("#create_role"),
			is_active: $("#is_active"),
			job_title: $("#job_title"),
			department: $("#department"),
		};
	}

	function editFieldMap() {
		return {
			firstname: $("#edit_firstname"),
			lastname: $("#edit_lastname"),
			employee_id: $("#edit_employee_id"),
			birthday: $("#edit_birthday"),
			contactno: $("#edit_contactno"),
			address: $("#edit_address"),
			email: $("#edit_email"),
			role: $("#edit_role"),
			is_active: $("#edit_is_active"),
			job_title: $("#edit_job_title"),
			department: $("#edit_department"),
		};
	}

	/* ══════════════════════════════════════════════════════════════════
       CREATE USER
       ══════════════════════════════════════════════════════════════════ */
	window.createUser = function () {
		clearAllErrors("#createUserModal");
		var fm = createFieldMap();
		var formData = new FormData();

		formData.append("firstname", fm.firstname.val().trim());
		formData.append("lastname", fm.lastname.val().trim());
		formData.append("employee_id", fm.employee_id.val().trim());
		formData.append("birthday", fm.birthday.val());
		formData.append("contactno", fm.contactno.val().trim());
		formData.append("address", fm.address.val().trim());
		formData.append("email", fm.email.val().trim());
		formData.append("password", fm.password.val());
		formData.append("role", fm.role.val());
		formData.append("is_active", fm.is_active.val());
		formData.append("job_title", fm.job_title.val().trim());
		formData.append("department", fm.department.val().trim());
		formData.append($("#csrf_token_name").val(), $("#csrf_token_value").val());

		var file = $("#profile_picture")[0].files[0];
		if (file) formData.append("profile_picture", file);

		$.ajax({
			url: BASE_URL + "users/store",
			method: "POST",
			data: formData,
			processData: false,
			contentType: false,
			dataType: "json",
			success: function (res) {
				if (res.success) {
					$("#createUserModal").modal("hide");
					showToast("success", res.message || "User created successfully.");
					setTimeout(function () {
						table.ajax.reload(null, false);
					}, 800);
				} else {
					applyErrors("#createUserModal", "#createAlert", res.errors || {}, fm);
					if ($.isEmptyObject(res.errors) && res.message) {
						$("#createAlert")
							.html("<strong>&#9888; " + res.message + "</strong>")
							.removeClass()
							.addClass("alert alert-danger p-2 mt-1 mb-2")
							.show();
					}
				}
			},
			error: function () {
				$("#createAlert")
					.html("<strong>&#9888; Server error. Please try again.</strong>")
					.removeClass()
					.addClass("alert alert-danger p-2 mt-1 mb-2")
					.show();
			},
		});
	};
	$("#createUserModal").on("show.bs.modal", function () {
		$.ajax({
			url: BASE_URL + "users/generate_employee_id",
			type: "GET",
			dataType: "json",
			success: function (res) {
				if (res.success) {
					$("#employee_id").val(res.data.employee_id);
				}
			},
			error: function () {
				showToast("danger", "Failed to generate Employee ID.");
			},
		});
	});

	/* ══════════════════════════════════════════════════════════════════
       EDIT USER
       ══════════════════════════════════════════════════════════════════ */
	$(document).on("click", ".btn-edit", function () {
		var id = $(this).data("id"); // this is the hash
		$(".rms-dropdown-menu.show").removeClass("show");
		clearAllErrors("#editUserModal");

		$.ajax({
			url: BASE_URL + "users/get/" + id,
			method: "GET",
			dataType: "json",
			success: function (res) {
				if (!res.success) {
					showToast("danger", res.message || "Failed to load user.");
					return;
				}
				var u = res.data;
				$("#edit_id").val(id); // store the hash, not u.id
				$("#edit_firstname").val(u.firstname);
				$("#edit_lastname").val(u.lastname);
				$("#edit_employee_id").val(u.employee_id);
				$("#edit_birthday").val(u.birthday);
				$("#edit_contactno").val(u.contactno);
				$("#edit_address").val(u.address);
				$("#edit_email").val(u.email);
				$("#edit_role").val(u.role);
				$("#edit_is_active").val(u.is_active);
				$("#edit_job_title").val(u.job_title);
				$("#edit_department").val(u.department);

				// Password reset count
				var count = parseInt(u.password_reset_count) || 0;
				$("#edit_reset_count").text(count);
				$("#edit_reset_count_note").text(
					count === 0
						? "(never reset)"
						: count === 1
							? "(reset once)"
							: "(reset " + count + " times)",
				);

				$("#editUserModal").modal("show");
			},
			error: function () {
				showToast("danger", "Server error loading user.");
			},
		});
	});

	/* ══════════════════════════════════════════════════════════════════
       UPDATE USER
       ══════════════════════════════════════════════════════════════════ */
	window.updateUser = function () {
		clearAllErrors("#editUserModal");
		var fm = editFieldMap();
		var formData = new FormData();

		formData.append("id", $("#edit_id").val());
		formData.append("firstname", fm.firstname.val().trim());
		formData.append("lastname", fm.lastname.val().trim());
		formData.append("employee_id", fm.employee_id.val().trim());
		formData.append("birthday", fm.birthday.val());
		formData.append("contactno", fm.contactno.val().trim());
		formData.append("address", fm.address.val().trim());
		formData.append("email", fm.email.val().trim());
		formData.append("role", fm.role.val());
		formData.append("is_active", fm.is_active.val());
		formData.append("job_title", fm.job_title.val().trim());
		formData.append("department", fm.department.val().trim());
		formData.append($("#csrf_token_name").val(), $("#csrf_token_value").val());

		var file =
			$("#edit_profile_picture")[0] && $("#edit_profile_picture")[0].files[0];
		if (file) formData.append("profile_picture", file);

		$.ajax({
			url: BASE_URL + "users/update",
			method: "POST",
			data: formData,
			processData: false,
			contentType: false,
			dataType: "json",
			success: function (res) {
				if (res.success) {
					$("#editUserModal").modal("hide");
					showToast("success", res.message || "User updated successfully.");
					setTimeout(function () {
						table.ajax.reload(null, false);
					}, 800);
				} else {
					applyErrors("#editUserModal", "#editAlert", res.errors || {}, fm);
					if ($.isEmptyObject(res.errors) && res.message) {
						$("#editAlert")
							.html("<strong>&#9888; " + res.message + "</strong>")
							.removeClass()
							.addClass("alert alert-danger p-2 mt-1 mb-2")
							.show();
					}
				}
			},
			error: function () {
				$("#editAlert")
					.html("<strong>&#9888; Server error. Please try again.</strong>")
					.removeClass()
					.addClass("alert alert-danger p-2 mt-1 mb-2")
					.show();
			},
		});
	};

	/* ══════════════════════════════════════════════════════════════════
       DELETE USER
       ══════════════════════════════════════════════════════════════════ */
	$(document).on("click", ".btn-delete", function () {
		var id = $(this).data("id");
		$(".rms-dropdown-menu.show").removeClass("show");

		Swal.fire({
			title: "Are you sure?",
			text: "This user will be soft-deleted.",
			icon: "warning",
			showCancelButton: true,
			confirmButtonColor: "#d33",
			cancelButtonColor: "#3085d6",
			confirmButtonText: "Yes, delete it!",
		}).then(function (result) {
			if (!result.isConfirmed) return;
			$.ajax({
				url: BASE_URL + "users/delete/" + id,
				method: "POST",
				data: csrfData(),
				dataType: "json",
				success: function (res) {
					if (res.success) {
						showToast("success", res.message || "User deleted.");
						setTimeout(function () {
							table.ajax.reload(null, false);
						}, 800);
					} else {
						showToast("danger", res.message || "Delete failed.");
					}
				},
				error: function () {
					showToast("danger", "Server error. Please try again.");
				},
			});
		});
	});

	/* ══════════════════════════════════════════════════════════════════
       RESET PASSWORD
       ══════════════════════════════════════════════════════════════════ */
	$(document).on("click", ".btn-reset-password", function () {
		var id = $(this).data("id");
		$(".rms-dropdown-menu.show").removeClass("show");

		Swal.fire({
			title: "Reset password?",
			html: "Password will be reset to: <strong>rms-2026</strong><br>The user will be required to change it on next login.",
			icon: "warning",
			showCancelButton: true,
			confirmButtonColor: "#f0ad4e",
			cancelButtonColor: "#6c757d",
			confirmButtonText: "Yes, reset it!",
		}).then(function (result) {
			if (!result.isConfirmed) return;

			$.ajax({
				url: BASE_URL + "users/reset_password/" + id,
				method: "POST",
				data: csrfData(),
				dataType: "json",
				success: function (res) {
					if (res.success) {
						showToast("success", res.message || "Password reset successfully.");
						setTimeout(function () {
							table.ajax.reload(null, false);
						}, 800);
					} else {
						showToast("danger", res.message || "Reset failed.");
					}
				},
				error: function () {
					showToast("danger", "Server error. Please try again.");
				},
			});
		});
	});

	/* ══════════════════════════════════════════════════════════════════
       TOAST
       ══════════════════════════════════════════════════════════════════ */
	function showToast(type, message) {
		var bg = {
			success: "#28a745",
			danger: "#dc3545",
			warning: "#ffc107",
			info: "#17a2b8",
		};
		var $toast = $("<div>")
			.css({
				position: "fixed",
				bottom: "24px",
				right: "24px",
				zIndex: 99999,
				background: bg[type] || "#333",
				color: "#fff",
				padding: "10px 18px",
				borderRadius: "6px",
				fontSize: ".875rem",
				boxShadow: "0 4px 12px rgba(0,0,0,.25)",
				maxWidth: "320px",
				opacity: 0,
				transition: "opacity .25s",
			})
			.text(message)
			.appendTo("body");

		setTimeout(function () {
			$toast.css("opacity", 1);
		}, 20);
		setTimeout(function () {
			$toast.css("opacity", 0);
			setTimeout(function () {
				$toast.remove();
			}, 300);
		}, 3200);
	}
	/* ══════════════════════════════════════════════════════════════════
       JS:OPEN MODAL + STORE ENCRYPTED ID
       ══════════════════════════════════════════════════════════════════ */
	$(document).on("click", ".btn-attach-docs", function () {
		let id = $(this).data("id");

		$("#attach_user_id").val(id);

		$("#attachDocsModal").modal("show");

		loadUserDocs(id);
	});
	// preview
	$(document).on("change", "#doc_file", function () {
		const files = this.files;
		const preview = $("#filePreview");

		preview.html("");

		if (!files.length) {
			preview.html('<small class="text-muted">No files selected.</small>');
			return;
		}

		Array.from(files).forEach((file) => {
			const ext = file.name.split(".").pop().toLowerCase();

			// IMAGE PREVIEW
			if (["jpg", "jpeg", "png"].includes(ext)) {
				const reader = new FileReader();

				reader.onload = function (e) {
					preview.append(`
						<div style="width:120px;text-align:center;margin:5px;">
							<img src="${e.target.result}"
								 style="width:100%;height:100px;object-fit:cover;border-radius:6px;border:1px solid #ccc;">
							<small style="display:block;word-break:break-word;">${file.name}</small>
						</div>
					`);
				};

				reader.readAsDataURL(file);
			} else {
				// NON-IMAGE FILES
				preview.append(`
					<div class="p-2 border rounded bg-white mb-1">
						📄 ${file.name}
					</div>
				`);
			}
		});
	});
	$(document).on;

	function loadUserDocs(userId) {
		$.get(
			BASE_URL + "users/get_user_docs/" + encodeURIComponent(userId),
			function (res) {
				const box = $("#uploadedFiles");
				box.html("");

				if (!res.data || !res.data.length) {
					box.html('<small class="text-muted">No files uploaded yet.</small>');
					return;
				}

				res.data.forEach((doc) => {
					const icon = getFileIcon(doc.file_type, doc.file_name);

					box.append(`
					<div class="drive-card">
	
						<div class="drive-actions">
							<button class="btn btn-sm btn-light">⋮</button>
						</div>
	
						<div class="drive-icon">
							${icon}
						</div>
	
						<div class="drive-name">
							${doc.file_name}
						</div>
	
					</div>
				`);
				});
			},
		);
	}
});
