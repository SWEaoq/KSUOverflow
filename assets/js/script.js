$(document).ready(function () {
    const sampleQuestions = [
        { id: 1, title: "How to connect PHP with MySQL?", author: "Ali", date: "Mar 5, 2025" },
        { id: 2, title: "Best practices for vanilla JavaScript?", author: "Fatima", date: "Mar 4, 2025" }
    ];

    sampleQuestions.forEach(q => {
        $("#questions-list").append(`
            <div class="question p-3">
                <a href="question.html?id=${q.id}" class="question-title">${q.title}</a>
                <p class="text-muted">Asked by ${q.author} on ${q.date}</p>
            </div>
        `);
    });
});
