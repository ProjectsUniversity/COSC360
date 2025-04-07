<?php
header('Content-Type: application/json');
require_once 'config.php';

try {
    // Get search query from GET parameters
    $query = isset($_GET['query']) ? trim($_GET['query']) : '';
    
    if (empty($query)) {
        echo json_encode([]);
        exit;
    }
    
    // Prepare the SQL query with LIKE for title search
    $sql = "SELECT j.*, e.company_name 
            FROM jobs j 
            JOIN employers e ON j.employer_id = e.employer_id 
            WHERE j.title LIKE ? 
            AND j.status = 'active' 
            ORDER BY j.created_at DESC 
            LIMIT 10";
    
    $stmt = $pdo->prepare($sql);
    $searchParam = '%' . $query . '%';
    $stmt->execute([$searchParam]);
    
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Log the results for debugging
    error_log("Search query: " . $query);
    error_log("Number of results: " . count($results));
    
    echo json_encode($results);
} catch (PDOException $e) {
    error_log("Database error in search-jobs.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'error' => 'Database error occurred',
        'message' => $e->getMessage()
    ]);
} catch (Exception $e) {
    error_log("General error in search-jobs.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'error' => 'An error occurred',
        'message' => $e->getMessage()
    ]);
}
?> 