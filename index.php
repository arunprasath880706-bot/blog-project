<?php
// ============================================
// MAIN PAGE - BLOG POSTS
// Task 3: Search + Pagination
// ============================================

session_start();
require_once 'config/database.php';

$page_title = 'Home - Blog';

// Get search term (READ-ONLY - no database changes)
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Pagination variables
$posts_per_page = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $posts_per_page;

try {
    // Build query based on search
    if (!empty($search)) {
        // COUNT posts with search
        $count_query = "SELECT COUNT(*) FROM posts 
                        WHERE title LIKE :search OR content LIKE :search";
        $count_stmt = $pdo->prepare($count_query);
        $count_stmt->execute(['search' => "%$search%"]);
        $total_posts = $count_stmt->fetchColumn();
        
        // GET posts with search
        $query = "SELECT * FROM posts 
                  WHERE title LIKE :search OR content LIKE :search 
                  ORDER BY created_at DESC 
                  LIMIT :limit OFFSET :offset";
        $stmt = $pdo->prepare($query);
        $stmt->bindValue(':search', "%$search%");
        $stmt->bindValue(':limit', $posts_per_page, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    } else {
        // COUNT all posts
        $total_posts = $pdo->query("SELECT COUNT(*) FROM posts")->fetchColumn();
        
        // GET all posts with pagination
        $query = "SELECT * FROM posts 
                  ORDER BY created_at DESC 
                  LIMIT :limit OFFSET :offset";
        $stmt = $pdo->prepare($query);
        $stmt->bindValue(':limit', $posts_per_page, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    }
    
    $stmt->execute();
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Calculate total pages
    $total_pages = ($total_posts > 0) ? ceil($total_posts / $posts_per_page) : 1;
    
} catch(PDOException $e) {
    $posts = [];
    $total_pages = 1;
    $error = "Database error: " . $e->getMessage();
}

// Include header
include 'includes/header.php';
?>

<!-- Main Content -->
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <!-- Page Title -->
            <h1 class="text-center mb-4">
                <i class="fas fa-newspaper text-primary"></i> 
                Blog Posts
            </h1>
            
            <!-- Search Form -->
            <div class="search-wrapper">
                <form method="GET" action="index.php" class="d-flex flex-wrap gap-2">
                    <div class="input-group flex-grow-1">
                        <span class="input-group-text">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" name="search" class="form-control" 
                               placeholder="Search posts by title or content..." 
                               value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Search
                    </button>
                    <?php if(!empty($search)): ?>
                        <a href="index.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Clear
                        </a>
                    <?php endif; ?>
                </form>
            </div>

            <!-- Search Results Badge -->
            <?php if(!empty($search)): ?>
                <div class="text-center">
                    <span class="search-badge">
                        <i class="fas fa-search"></i> 
                        Results for: "<?php echo htmlspecialchars($search); ?>"
                        <span class="badge bg-light text-dark ms-2">
                            <?php echo $total_posts; ?> found
                        </span>
                    </span>
                </div>
            <?php endif; ?>

            <!-- Display Error or Posts -->
            <?php if(isset($error)): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> 
                    <?php echo $error; ?>
                </div>
            <?php elseif(count($posts) > 0): ?>
                <!-- Posts Loop -->
                <?php foreach($posts as $post): ?>
                    <div class="card post-card">
                        <div class="card-body">
                            <h5 class="card-title">
                                <i class="fas fa-file-alt"></i>
                                <?php echo htmlspecialchars($post['title']); ?>
                            </h5>
                            <p class="card-text">
                                <?php 
                                    $content = htmlspecialchars($post['content']);
                                    echo (strlen($content) > 200) 
                                        ? substr($content, 0, 200) . '...' 
                                        : $content;
                                ?>
                            </p>
                            <div class="d-flex flex-wrap justify-content-between align-items-center">
                                <small class="text-muted">
                                    <i class="far fa-calendar-alt"></i>
                                    <?php echo date('F j, Y', strtotime($post['created_at'])); ?>
                                </small>
                                <div class="btn-group">
                                    <a href="edit.php?id=<?php echo $post['id']; ?>" 
                                       class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <a href="delete.php?id=<?php echo $post['id']; ?>" 
                                       class="btn btn-danger btn-sm" 
                                       onclick="return confirm('Are you sure you want to delete this post?')">
                                        <i class="fas fa-trash"></i> Delete
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

                <!-- Pagination -->
                <?php if($total_pages > 1): ?>
                    <div class="pagination-wrapper">
                        <nav aria-label="Page navigation">
                            <ul class="pagination justify-content-center">
                                <!-- Previous Button -->
                                <?php if($page > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" 
                                           href="?page=<?php echo $page-1; ?>&search=<?php echo urlencode($search); ?>">
                                            <i class="fas fa-chevron-left"></i> Prev
                                        </a>
                                    </li>
                                <?php else: ?>
                                    <li class="page-item disabled">
                                        <span class="page-link"><i class="fas fa-chevron-left"></i> Prev</span>
                                    </li>
                                <?php endif; ?>
                                
                                <!-- Page Numbers -->
                                <?php 
                                    $start_page = max(1, $page - 2);
                                    $end_page = min($total_pages, $page + 2);
                                    
                                    if($start_page > 1) {
                                        echo '<li class="page-item">
                                                <a class="page-link" href="?page=1&search='.urlencode($search).'">1</a>
                                              </li>';
                                        if($start_page > 2) {
                                            echo '<li class="page-item disabled">
                                                    <span class="page-link">...</span>
                                                  </li>';
                                        }
                                    }
                                    
                                    for($i = $start_page; $i <= $end_page; $i++): 
                                ?>
                                    <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                                        <a class="page-link" 
                                           href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>">
                                            <?php echo $i; ?>
                                        </a>
                                    </li>
                                <?php endfor; 
                                    
                                    if($end_page < $total_pages) {
                                        if($end_page < $total_pages - 1) {
                                            echo '<li class="page-item disabled">
                                                    <span class="page-link">...</span>
                                                  </li>';
                                        }
                                        echo '<li class="page-item">
                                                <a class="page-link" href="?page='.$total_pages.'&search='.urlencode($search).'">
                                                    '.$total_pages.'
                                                </a>
                                              </li>';
                                    }
                                ?>
                                
                                <!-- Next Button -->
                                <?php if($page < $total_pages): ?>
                                    <li class="page-item">
                                        <a class="page-link" 
                                           href="?page=<?php echo $page+1; ?>&search=<?php echo urlencode($search); ?>">
                                            Next <i class="fas fa-chevron-right"></i>
                                        </a>
                                    </li>
                                <?php else: ?>
                                    <li class="page-item disabled">
                                        <span class="page-link">Next <i class="fas fa-chevron-right"></i></span>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                        
                        <!-- Page Info -->
                        <div class="text-center page-info">
                            <i class="fas fa-info-circle"></i> 
                            Page <?php echo $page; ?> of <?php echo $total_pages; ?> 
                            <span class="mx-2">|</span>
                            <i class="fas fa-file-alt"></i> 
                            <?php echo $total_posts; ?> total posts
                        </div>
                    </div>
                <?php endif; ?>

            <?php else: ?>
                <!-- No Posts -->
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle"></i>
                    <?php if(!empty($search)): ?>
                        No posts found matching 
                        "<strong><?php echo htmlspecialchars($search); ?></strong>"
                    <?php else: ?>
                        No posts yet. 
                        <a href="create.php" class="alert-link">
                            <i class="fas fa-plus-circle"></i> Create your first post!
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>