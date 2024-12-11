<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Todo List</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f9f9f9;
            color: #333;
        }

        h1 {
            text-align: center;
            color: #555;
        }

        form {
            margin: 20px 0;
            text-align: center;
        }

        input[type="text"] {
            padding: 8px;
            width: 200px;
            margin-right: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        input[type="submit"] {
            padding: 8px 16px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }

        button {
            padding: 8px 16px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 12px;
            text-align: center;
        }

        th {
            background-color: #f4f4f4;
            color: #333;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        a {
            color: #007BFF;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        .no-data {
            text-align: center;
            color: #888;
            font-style: italic;
        }

        .checkbox-done {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .checkbox-done input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <h1>Todo List</h1>
    <form method="post" action='/index.php/save'>
        @csrf
        <input type='text' id='todo' name='todo' placeholder="Enter your todo">
        <input type='submit' value='Submit'>
    </form>
    <form method='get' action=''>
        <input type='text' name='key' placeholder="Search..." value={{$gets['key']??''}}>
        <input type='submit' value='Search'>
        <button type='button' onclick='clearKey()'>ShowAll</button>
    </form>
    <table>
        <tr>
            <th>Done</th>
            <th>ID</th>
            <th>Todo</th>
            <th>Date</th>
            <th>Operation</th>
        </tr>
        @forelse($todolist as $v)
        <tr data-id="{{ $v['id'] }}">
            <td class="checkbox-done">
                <input type="checkbox" 
                class="done-checkbox" 
                @if($v['done']) checked @endif 
                onclick="toggleDone(@json($v['id']))">
            </td>
            <td>{{$v['id']}}</td>
            <td class="todo-cell">
                @if($v['done'])
                <s>{{$v['todo']}}</s>
                @else
                <span id="todo-text-{{ $v['id'] }}">{{ $v['todo'] }}</span>
                <input type="text" id="todo-input-{{ $v['id'] }}" value="{{ $v['todo'] }}" style="display: none;">
                @endif
            </td>
            <td>{{$v['datetime']}}</td>
            <td>
                <button onclick="editTodo({{ $v['id'] }})">Edit</button>
                <button onclick="updateTodo({{ $v['id'] }})" style="display: none;" id="update-btn-{{ $v['id'] }}">Update</button>
                <a href="/delete/{{$v['id']}}">Delete</a>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="5" class="no-data">No data</td>
        </tr>
        @endforelse
    </table>

    <script>
    function toggleDone(id) {
        fetch(`/todo/${id}/toggle`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            console.log(data.message);

            const row = document.querySelector(`tr[data-id="${id}"]`);
            if (row) {
                const todoCell = row.querySelector('.todo-cell');
                const checkbox = row.querySelector('.done-checkbox');

                if (data.done) {
                    todoCell.innerHTML = `<s>${todoCell.textContent}</s>`;
                } else {
                    todoCell.innerHTML = todoCell.textContent.replace(/<s>|<\/s>/g, '');
                }

                checkbox.checked = data.done;
            }
        })
        .catch(error => console.error('Error:', error));
    }

    function clearKey() {
        const url = new URL(window.location.href);
        url.searchParams.delete('key'); 
        window.location.href = url.toString();
    }

    function editTodo(id) {
        document.getElementById(`todo-text-${id}`).style.display = 'none';
        document.getElementById(`todo-input-${id}`).style.display = 'inline';
        document.getElementById(`update-btn-${id}`).style.display = 'inline';
    }

    function updateTodo(id) {
        const updatedTodo = document.getElementById(`todo-input-${id}`).value;

        fetch(`/todo/${id}/update`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
            body: JSON.stringify({ todo: updatedTodo }),
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                document.getElementById(`todo-text-${id}`).innerText = updatedTodo;
                document.getElementById(`todo-text-${id}`).style.display = 'inline';
                document.getElementById(`todo-input-${id}`).style.display = 'none';
                document.getElementById(`update-btn-${id}`).style.display = 'none';
                alert(data.message);
            } else {
                alert(data.message);
            }
        })
        .catch(error => console.error('Error:', error));
    }

    </script>
</body>
</html>
