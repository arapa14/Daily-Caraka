<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Location Management</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="bg-gray-100 p-8">
    <!-- Header -->
    <div class="bg-white shadow-md rounded-lg p-4 flex flex-col sm:flex-row justify-between items-center">
        <a href="{{ route('dashboard') }}"
            class="text-xl font-bold text-blue-600 mb-2 sm:mb-0 hover:bg-blue-100 rounded-xl px-2 py-2">
            Dashboard</a>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="text-red-500 hover:text-red-700">
                <i class="fas fa-sign-out-alt"></i> Logout
            </button>
        </form>
    </div>

    <!-- Container -->
    <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-md mt-6">
        <h2 class="text-2xl font-bold text-gray-700 mb-6">Location Management</h2>

        <!-- Form Tambah Location -->
        <form id="createLocationForm" class="mb-6">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <input type="text" id="location"
                    class="border border-gray-300 p-3 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Location" required>
            </div>
            <button type="submit"
                class="mt-4 bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-md shadow-md flex items-center gap-2 justify-center">
                <i class="fas fa-plus"></i> Create Location
            </button>
        </form>

        <!-- Search Field -->
        <div class="mb-4">
            <input type="text" id="searchInput" placeholder="Cari location..."
                class="w-full border border-gray-300 p-3 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <!-- Tabel Location -->
        <div class="overflow-x-auto">
            <table class="w-full border-collapse border border-gray-300" id="locationTable">
                <thead>
                    <tr class="bg-gray-200 text-gray-700">
                        <th class="border p-3">ID</th>
                        <th class="border p-3">Location</th>
                        <th class="border p-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($locations as $location)
                        <tr id="locationRow-{{ $location->id }}" class="text-center border">
                            <td class="border p-3">{{ $location->id }}</td>
                            <td class="border p-3" id="locationName-{{ $location->id }}">{{ $location->location }}</td>
                            <td class="border p-3 flex justify-center gap-2">
                                <!-- Simpan data location ke data attribute -->
                                <button data-location='@json($location)' onclick="editLocation(this)"
                                    class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded-md shadow-md">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <button onclick="deleteLocation({{ $location->id }})"
                                    class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-md shadow-md">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- JavaScript untuk CRUD dan Search -->
    <script>
        // Create Location
        document.getElementById('createLocationForm').addEventListener('submit', function(event) {
            event.preventDefault();
            const locationName = document.getElementById('location').value;

            fetch("{{ route('location.store') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        location: locationName
                    }) // Perhatikan di sini: key-nya "location"
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire("Success!", "Location created successfully!", "success")
                            .then(() => location.reload());
                    } else {
                        Swal.fire("Error!", data.error, "error");
                    }
                });
        });


        // Delete Location
        function deleteLocation(locationId) {
            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, delete it!",
                cancelButtonText: "Cancel"
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/location/${locationId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                document.getElementById(`locationRow-${locationId}`).remove();
                                Swal.fire("Deleted!", "Location has been deleted.", "success");
                            } else {
                                Swal.fire("Error!", data.error, "error");
                            }
                        });
                }
            });
        }

        // Edit Location
        function editLocation(button) {
            const locationData = JSON.parse(button.getAttribute('data-location'));

            Swal.fire({
                title: 'Edit Location',
                html: `<input id="swal-input1" class="swal2-input border border-gray-300 p-3 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Location" value="${locationData.location}">`,
                focusConfirm: false,
                showCancelButton: true,
                confirmButtonText: "Update",
                cancelButtonText: "Cancel",
                preConfirm: () => {
                    return {
                        location: document.getElementById('swal-input1').value,
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const {
                        location
                    } = result.value;
                    fetch(`/location/${locationData.id}`, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                location
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                document.getElementById(`locationName-${locationData.id}`).textContent = data
                                    .location.location;
                                Swal.fire("Updated!", "Location has been updated.", "success");
                            } else {
                                Swal.fire("Error!", data.error, "error");
                            }
                        });
                }
            });
        }


        // Search Functionality
        document.getElementById('searchInput').addEventListener('keyup', function() {
            const filter = this.value.toLowerCase();
            const rows = document.querySelectorAll('#locationTable tbody tr');

            rows.forEach(row => {
                // Gabungkan semua teks pada baris
                const rowText = row.textContent.toLowerCase();
                row.style.display = rowText.indexOf(filter) > -1 ? '' : 'none';
            });
        });
    </script>
</body>

</html>
