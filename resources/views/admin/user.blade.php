<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>User Management</title>
  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="bg-gray-100 p-8">

  {{-- Header --}}
  <div class="bg-white shadow-md rounded-lg p-4 flex flex-col sm:flex-row justify-between items-center">
    <h1 class="text-xl font-bold text-blue-600 mb-2 sm:mb-0">Dashboard</h1>
    <form action="{{ route('logout') }}" method="POST">
      @csrf
      <button type="submit" class="text-red-500 hover:text-red-700">
        <i class="fas fa-sign-out-alt"></i> Logout
      </button>
    </form>
  </div>

  {{-- Container --}}
  <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-md mt-6">
    <h2 class="text-2xl font-bold text-gray-700 mb-6">User Management</h2>

    {{-- Form Tambah User --}}
    <form id="createUserForm" class="mb-6">
      @csrf
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <input type="text" id="name" class="border border-gray-300 p-3 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Name" required>
        <input type="email" id="email" class="border border-gray-300 p-3 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Email" required>
        <input type="password" id="password" class="border border-gray-300 p-3 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Password" required>
        <select id="role" class="border border-gray-300 p-3 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
          <option value="admin">Admin</option>
          <option value="reviewer">Reviewer</option>
          <option value="caraka" selected>Caraka</option>
        </select>
      </div>
      <button type="submit" class="mt-4 bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-md shadow-md flex items-center gap-2 justify-center">
        <i class="fas fa-user-plus"></i> Create User
      </button>
    </form>

    {{-- Tabel User --}}
    <div class="overflow-x-auto">
      <table class="w-full border-collapse border border-gray-300">
        <thead>
          <tr class="bg-gray-200 text-gray-700">
            <th class="border p-3">ID</th>
            <th class="border p-3">Name</th>
            <th class="border p-3">Email</th>
            <th class="border p-3">Role</th>
            <th class="border p-3">Actions</th>
          </tr>
        </thead>
        <tbody id="userTable">
          @foreach ($users as $user)
            <tr id="userRow-{{ $user->id }}" class="text-center border">
              <td class="border p-3">{{ $user->id }}</td>
              <td class="border p-3" id="userName-{{ $user->id }}">{{ $user->name }}</td>
              <td class="border p-3" id="userEmail-{{ $user->id }}">{{ $user->email }}</td>
              <td class="border p-3" id="userRole-{{ $user->id }}">{{ ucfirst($user->role) }}</td>
              <td class="border p-3 flex justify-center gap-2">
                <!-- Simpan data user ke data attribute -->
                <button data-user='@json($user)' onclick="editUser(this)" class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded-md shadow-md">
                  <i class="fas fa-edit"></i> Edit
                </button>
                <button onclick="deleteUser({{ $user->id }})" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-md shadow-md">
                  <i class="fas fa-trash"></i> Delete
                </button>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

  </div>

  {{-- JavaScript untuk CRUD --}}
  <script>
    // Create User
    document.getElementById('createUserForm').addEventListener('submit', function (event) {
      event.preventDefault();
      const name = document.getElementById('name').value;
      const email = document.getElementById('email').value;
      const password = document.getElementById('password').value;
      const role = document.getElementById('role').value;

      fetch("{{ route('user.store') }}", {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
          name,
          email,
          password,
          role
        })
      })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            Swal.fire("Success!", "User created successfully!", "success")
              .then(() => location.reload());
          } else {
            Swal.fire("Error!", data.error, "error");
          }
        });
    });

    // Delete User
    function deleteUser(userId) {
      Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Yes, delete it!",
        cancelButtonText: "Cancel"
      }).then((result) => {
        if (result.isConfirmed) {
          fetch(`/user/${userId}`, {
            method: 'DELETE',
            headers: {
              'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
          })
            .then(response => response.json())
            .then(data => {
              if (data.success) {
                document.getElementById(`userRow-${userId}`).remove();
                Swal.fire("Deleted!", "User has been deleted.", "success");
              } else {
                Swal.fire("Error!", data.error, "error");
              }
            });
        }
      });
    }

    // Edit User
    function editUser(button) {
      // Ambil data JSON dari atribut data-user dan parsing ke objek
      const user = JSON.parse(button.getAttribute('data-user'));

      Swal.fire({
        title: 'Edit User',
        html:
          `<div class="flex flex-col gap-4 text-left">
            <input id="swal-input1" class="swal2-input border border-gray-300 p-3 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Name" value="${user.name}">
            <input id="swal-input2" class="swal2-input border border-gray-300 p-3 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Email" value="${user.email}">
            <input id="swal-input3" type="password" class="swal2-input border border-gray-300 p-3 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Password (kosongkan jika tidak diubah)">
            <select id="swal-input4" class="swal2-input border border-gray-300 p-3 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
              <option value="admin" ${user.role === 'admin' ? 'selected' : ''}>Admin</option>
              <option value="reviewer" ${user.role === 'reviewer' ? 'selected' : ''}>Reviewer</option>
              <option value="caraka" ${user.role === 'caraka' ? 'selected' : ''}>Caraka</option>
            </select>
          </div>`,
        focusConfirm: false,
        showCancelButton: true,
        confirmButtonText: "Update",
        cancelButtonText: "Cancel",
        preConfirm: () => {
          return {
            name: document.getElementById('swal-input1').value,
            email: document.getElementById('swal-input2').value,
            password: document.getElementById('swal-input3').value,
            role: document.getElementById('swal-input4').value
          }
        }
      }).then((result) => {
        if (result.isConfirmed) {
          const { name, email, password, role } = result.value;
          fetch(`/user/${user.id}`, {
            method: 'PUT',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ name, email, password, role })
          })
            .then(response => response.json())
            .then(data => {
              if (data.success) {
                // Update data di tabel tanpa reload halaman
                document.getElementById(`userName-${user.id}`).textContent = data.user.name;
                document.getElementById(`userEmail-${user.id}`).textContent = data.user.email;
                document.getElementById(`userRole-${user.id}`).textContent =
                  data.user.role.charAt(0).toUpperCase() + data.user.role.slice(1);
                Swal.fire("Updated!", "User has been updated.", "success");
              } else {
                Swal.fire("Error!", data.error, "error");
              }
            });
        }
      });
    }
  </script>
</body>

</html>
