document.addEventListener("DOMContentLoaded", () => {
    const tasks = document.querySelectorAll(".task");
    const columns = document.querySelectorAll(".column");

    tasks.forEach(task => {
        task.draggable = true;

        task.addEventListener("dragstart", e => {
            e.dataTransfer.setData("text/plain", task.dataset.id);
        });
    });

    columns.forEach(col => {
        col.addEventListener("dragover", e => e.preventDefault());

        col.addEventListener("drop", e => {
            e.preventDefault();
            const taskId = e.dataTransfer.getData("text/plain");
            const taskElement = document.querySelector(`[data-id='${taskId}']`);
            col.appendChild(taskElement);

            // Atualizar estado via AJAX
            const newState = col.dataset.status;

            fetch("update_task.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `id=${taskId}&status=${encodeURIComponent(newState)}`
            }).then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(`Tarefa atualizada para '${newState}'!`);
                } else {
                    alert('Erro ao atualizar a tarefa.');
                }
            });
        });
    });
});
