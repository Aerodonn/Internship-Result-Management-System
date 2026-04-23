<?php
    function createBook($title, $author, $year) {
        $sql = "INSERT INTO books (title, author, publication_year) VALUES (?, ?, ?)";
        $params = [$title, $author, $year];
        return executePreparedStatement($sql, $params);
    }
    function getBooks($condition = null) {
        $sql = "SELECT * FROM books";
        if ($condition) {
            $sql .= " WHERE $condition";
        }
        return executePreparedStatement($sql, []);
    }

    function updateBook($id, $title, $author, $year) {
        $sql = "UPDATE books SET title = ?, author = ?, publication_year = ? WHERE book_id = ?";
        $params = [$title, $author, $year, $id];
        return executePreparedStatement($sql, $params);
}

    function deleteBook($id) {
        $sql = "DELETE FROM books WHERE book_id = ?";
        $params = [$id];
        return executePreparedStatement($sql, $params);
    }

?>