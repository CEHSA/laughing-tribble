<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Dashboard - ArchiAxis</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <link href="../css/style.css" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com?plugins=typography,aspect-ratio,line-clamp,container-queries"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .material-icons-outlined { font-size: 20px; }
    </style>
</head>
<body class="h-full flex antialiased">

    <?php
        $current_view = isset($_GET['view']) ? $_GET['view'] : 'dashboard'; // Default to dashboard

        // Define menu items and their properties
        $menu_items = [
            'dashboard'     => ['icon' => 'dashboard', 'label' => 'Dashboard', 'href' => 'index.php?page=client_dashboard&view=dashboard'],
            'projects'      => ['icon' => 'assignment', 'label' => 'My Projects', 'href' => 'index.php?page=client_dashboard&view=projects'],
            'designs_files' => ['icon' => 'folder_open', 'label' => 'Designs & Files', 'href' => 'index.php?page=client_dashboard&view=designs_files'],
            'messages'      => ['icon' => 'chat_bubble_outline', 'label' => 'Messages', 'href' => 'index.php?page=client_dashboard&view=messages'],
            'billing'       => ['icon' => 'receipt_long', 'label' => 'Billing', 'href' => 'index.php?page=client_dashboard&view=billing'],
            'support'       => ['icon' => 'help_outline', 'label' => 'Support', 'href' => 'index.php?page=client_dashboard&view=support'],
        ];
    ?>

    <!-- Sidebar -->
    <aside id="sidebar" class="fixed inset-y-0 left-0 z-30 w-64 bg-slate-900 text-slate-200 flex flex-col transition-transform duration-300 ease-in-out transform -translate-x-full md:translate-x-0 md:relative">
        <div class="px-6 py-5 border-b border-slate-700">
            <a href="index.php?page=client_dashboard&view=dashboard" class="flex items-center gap-3">
                <img class="h-8 w-auto" src="https://tailwindui.com/img/logos/mark.svg?color=teal&shade=500" alt="ArchiAxis">
                <span class="text-xl font-semibold text-white">ArchiAxis</span>
            </a>
        </div>

        <nav class="flex-1 px-3 py-4 space-y-1.5">
            <?php foreach ($menu_items as $view_key => $item): ?>
                <a href="<?php echo htmlspecialchars($item['href']); ?>"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors
                          <?php echo ($current_view === $view_key) ? 'bg-teal-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white'; ?>">
                    <span class="material-icons-outlined"><?php echo htmlspecialchars($item['icon']); ?></span>
                    <span><?php echo htmlspecialchars($item['label']); ?></span>
                    <?php if ($view_key === 'messages'): ?>
                        <?php
                        // The $conversations variable is loaded when current_view is 'messages' inside the main content block.
                        // It's not available here when the sidebar is first rendered unless fetched globally earlier.
                        // For simplicity, using a placeholder or a count that might be from a previous load.
                        // A more robust solution involves a global data fetch or JS update.
                        $unread_message_count_for_badge = 0;
                        if (isset($GLOBALS['total_unread_messages_for_badge'])) { // Check if a global was set
                             $unread_message_count_for_badge = $GLOBALS['total_unread_messages_for_badge'];
                        }
                        if ($unread_message_count_for_badge > 0) : ?>
                            <span class="ml-auto bg-teal-500 text-white text-xs font-semibold px-2 py-0.5 rounded-full"><?php echo $unread_message_count_for_badge; ?></span>
                        <?php else: ?>
                            <!-- Static placeholder or no badge if count is 0 or unavailable -->
                             <!-- <span class="ml-auto bg-gray-400 text-white text-xs font-semibold px-2 py-0.5 rounded-full">0</span> -->
                        <?php endif; ?>
                    <?php endif; ?>
                </a>
            <?php endforeach; ?>
        </nav>

        <div class="px-3 py-4 mt-auto border-t border-slate-700">
            <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white">
                <span class="material-icons-outlined">account_circle</span>
                <span>Eleanor Vance</span>
            </a>
            <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white mt-1.5">
                <span class="material-icons-outlined">logout</span>
                <span>Logout</span>
            </a>
        </div>
    </aside>

    <!-- Main Content Wrapper -->
    <div class="flex-1 flex flex-col min-w-0">
        <!-- Mobile Header -->
        <header class="md:hidden sticky top-0 z-20 bg-white shadow-sm border-b border-slate-200">
            <div class="px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <a href="index.php?page=client_dashboard&view=dashboard" class="flex items-center gap-2">
                        <img class="h-7 w-auto" src="https://tailwindui.com/img/logos/mark.svg?color=teal&shade=500" alt="ArchiAxis">
                        <span class="text-lg font-semibold text-slate-800">ArchiAxis</span>
                    </a>
                    <button type="button" id="mobile-menu-button" class="p-2 text-slate-500 rounded-md hover:text-teal-600 hover:bg-slate-100">
                        <span class="sr-only">Open menu</span>
                        <span class="material-icons-outlined">menu</span>
                    </button>
                </div>
            </div>
        </header>

        <!-- Main content area where PHP will include views -->
        <main class="flex-1 p-6 md:p-8 overflow-y-auto bg-slate-50">
            <?php
            // $current_view is already defined at the top

            if ($current_view === 'messages') {
                // Page-specific header for Messages
                echo '<header class="mb-6">';
                echo '  <div class="flex flex-col md:flex-row justify-between md:items-center gap-4">';
                echo '    <div>';
                echo '      <div class="flex items-center gap-2 text-sm text-slate-500 mb-1">';
                echo '        <a href="index.php?page=client_dashboard&view=messages" class="hover:text-teal-600">Messages</a>';
                echo '      </div>';
                echo '      <h1 class="text-3xl font-bold text-slate-900">Your Conversations</h1>';
                echo '    </div>';
                echo '    <div class="flex items-center gap-3">';
                echo '      <button class="bg-white text-teal-600 border border-teal-600 px-4 py-2.5 rounded-lg text-sm font-medium hover:bg-teal-50 transition-colors flex items-center gap-2">';
                echo '        <span class="material-icons-outlined !text-base">search</span> Search Messages';
                echo '      </button>';
                echo '      <button class="bg-teal-600 text-white px-4 py-2.5 rounded-lg text-sm font-medium hover:bg-teal-700 transition-colors flex items-center gap-2">';
                echo '        <span class="material-icons-outlined !text-base">add_comment</span> New Message';
                echo '      </button>';
                echo '    </div>';
                echo '  </div>';
                echo '</header>';

                // For messages view, first include the PHP logic to fetch data
                // For messages view, first include the PHP logic to fetch data
                include __DIR__ . '/client/messages_content.php';
                // Now $conversations, $active_chat_messages, $active_chat_with_user, $current_user_id are available.
                // $GLOBALS['total_unread_messages_for_badge'] = 0; // Example of setting it for next load
                // if(isset($conversations)) { foreach($conversations as $c) { $GLOBALS['total_unread_messages_for_badge'] += $c['unread_count']; }}

                // Then, display the HTML structure for the messages UI
                if ($active_chat_with_user || !empty($conversations)) : // Check if there's anything to display
                ?>
                <div class="grid grid-cols-1 @container lg:grid-cols-[minmax(300px,400px)_1fr] gap-6 h-[calc(100vh-180px)] md:h-[calc(100vh-160px)]">
                    <!-- Conversations List (Left Panel) -->
                    <div class="bg-white rounded-xl border border-slate-200 shadow-sm flex flex-col">
                        <div class="p-4 border-b border-slate-200">
                            <h2 class="text-lg font-semibold text-slate-800">All Messages</h2>
                            <div class="mt-3 relative">
                                <span class="material-icons-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 !text-xl">search</span>
                                <input class="w-full pl-10 pr-3 py-2 rounded-md border border-slate-300 focus:ring-teal-500 focus:border-teal-500 text-sm" placeholder="Search conversations..." type="text"/>
                            </div>
                        </div>
                        <div class="flex-grow overflow-y-auto">
                            <ul class="divide-y divide-slate-200">
                                <?php if (empty($conversations)): ?>
                                    <li class="p-4 text-center text-slate-500">No conversations yet.</li>
                                <?php else: ?>
                                    <?php foreach ($conversations as $index => $convo): ?>
                                        <?php
                                        $is_active_conversation = ($active_chat_with_user && $convo['user_id'] == $active_chat_with_user['user_id']);
                                        $avatar_url = !empty($convo['avatar_url']) ? htmlspecialchars($convo['avatar_url']) : 'https://ui-avatars.com/api/?name=' . urlencode($convo['full_name']) . '&color=FFFFFF&background=0D9488';
                                        ?>
                                        <li class="<?php echo $is_active_conversation ? 'bg-teal-50 border-l-4 border-teal-500' : ''; ?> p-4 hover:bg-slate-50 cursor-pointer">
                                            <a href="index.php?page=client_dashboard&view=messages&chat_with=<?php echo $convo['user_id']; ?>" class="block">
                                                <div class="flex items-center gap-3">
                                                    <div class="relative">
                                                        <img alt="<?php echo htmlspecialchars($convo['full_name']); ?>" class="h-10 w-10 rounded-full object-cover" src="<?php echo $avatar_url; ?>"/>
                                                        <?php if (rand(0,1)): // Simulate online status ?>
                                                            <span class="absolute bottom-0 right-0 block h-2.5 w-2.5 rounded-full bg-green-500 ring-2 ring-white"></span>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <div class="flex justify-between items-center">
                                                            <h3 class="text-sm font-semibold text-slate-800 truncate"><?php echo htmlspecialchars($convo['full_name']); ?> <span class="text-xs text-slate-500 font-normal">(<?php echo htmlspecialchars($convo['role'] ?? 'User'); ?>)</span></h3>
                                                            <span class="text-xs text-slate-400 whitespace-nowrap"><?php echo date('g:i A', strtotime($convo['latest_message_timestamp'])); ?></span>
                                                        </div>
                                                        <p class="text-sm <?php echo ($convo['unread_count'] > 0 && !$is_active_conversation) ? 'text-teal-600 font-medium' : 'text-slate-500'; ?> truncate">
                                                            <?php echo htmlspecialchars($convo['latest_message']); ?>
                                                        </p>
                                                    </div>
                                                    <?php if ($convo['unread_count'] > 0 && !$is_active_conversation): ?>
                                                        <span class="bg-teal-500 text-white text-xs font-semibold px-1.5 py-0.5 rounded-full"><?php echo $convo['unread_count']; ?></span>
                                                    <?php endif; ?>
                                                </div>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>

                    <!-- Chat Panel (Right Panel) -->
                    <?php if ($active_chat_with_user): ?>
                    <div class="bg-white rounded-xl border border-slate-200 shadow-sm flex flex-col">
                        <div class="p-4 border-b border-slate-200 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <?php
                                $chat_partner_avatar = !empty($active_chat_with_user['avatar_url']) ? htmlspecialchars($active_chat_with_user['avatar_url']) : 'https://ui-avatars.com/api/?name=' . urlencode($active_chat_with_user['full_name']) . '&color=FFFFFF&background=0D9488';
                                ?>
                                <img alt="<?php echo htmlspecialchars($active_chat_with_user['full_name']); ?>" class="h-10 w-10 rounded-full object-cover" src="<?php echo $chat_partner_avatar; ?>"/>
                                <div>
                                    <h2 class="text-lg font-semibold text-slate-800"><?php echo htmlspecialchars($active_chat_with_user['full_name']); ?></h2>
                                    <p class="text-sm text-teal-600"><?php echo htmlspecialchars($active_chat_with_user['role'] ?? 'User'); ?> - <span class="text-green-500">Online</span></p> <?php // Fake online status ?>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <button class="text-slate-500 hover:text-teal-600 p-2 rounded-full hover:bg-slate-100"><span class="material-icons-outlined !text-xl">call</span></button>
                                <button class="text-slate-500 hover:text-teal-600 p-2 rounded-full hover:bg-slate-100"><span class="material-icons-outlined !text-xl">videocam</span></button>
                                <button class="text-slate-500 hover:text-teal-600 p-2 rounded-full hover:bg-slate-100"><span class="material-icons-outlined !text-xl">more_vert</span></button>
                            </div>
                        </div>
                        <div class="flex-grow overflow-y-auto p-6 space-y-4 bg-slate-50">
                            <?php if (empty($active_chat_messages)): ?>
                                <div class="text-center text-slate-500">No messages in this conversation yet. Say hello!</div>
                            <?php else: ?>
                                <?php
                                $last_date = null;
                                foreach ($active_chat_messages as $message):
                                    $message_date = date('Y-m-d', strtotime($message['timestamp']));
                                    if ($message_date !== $last_date) {
                                        echo '<div class="flex justify-center"><span class="px-2 py-1 bg-slate-200 text-slate-600 rounded-full text-xs">'.date('M j, Y', strtotime($message_date)).'</span></div>';
                                        $last_date = $message_date;
                                    }
                                    $is_current_user_sender = ($message['sender_id'] == $current_user_id);
                                    $sender_avatar = !empty($message['sender_avatar']) ? htmlspecialchars($message['sender_avatar']) : 'https://ui-avatars.com/api/?name=' . urlencode($message['sender_name']) . '&color=FFFFFF&background=0D9488';
                                ?>
                                <div class="flex gap-3 <?php echo $is_current_user_sender ? 'flex-row-reverse' : ''; ?>">
                                    <img alt="<?php echo htmlspecialchars($message['sender_name']); ?>" class="h-8 w-8 rounded-full object-cover" src="<?php echo $sender_avatar; ?>"/>
                                    <div class="<?php echo $is_current_user_sender ? 'bg-teal-600 text-white' : 'bg-white text-slate-700'; ?> p-3 rounded-lg <?php echo $is_current_user_sender ? 'rounded-br-none' : 'rounded-bl-none'; ?> shadow-sm max-w-md">
                                        <p class="text-sm"><?php echo nl2br(htmlspecialchars($message['message_content'])); ?></p>
                                        <p class="text-xs <?php echo $is_current_user_sender ? 'text-teal-100' : 'text-slate-400'; ?> mt-1 text-right"><?php echo date('g:i A', strtotime($message['timestamp'])); ?></p>
                                    </div>
                                    <div class="flex-1"></div> <?php // Spacer to push message to one side ?>
                                </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <div class="p-4 border-t border-slate-200 bg-white">
                            <form method="POST" action="includes/client/handle_send_message.php" class="flex items-center gap-2">
                                <input type="hidden" name="receiver_id" value="<?php echo htmlspecialchars($active_chat_with_user['user_id']); ?>">
                                <input type="hidden" name="current_view_url" value="<?php echo htmlspecialchars("index.php?page=client_dashboard&view=messages&chat_with=".$active_chat_with_user['user_id']); ?>">

                                <button type="button" class="text-slate-500 hover:text-teal-600 p-2 rounded-full hover:bg-slate-100"><span class="material-icons-outlined">add_photo_alternate</span></button>
                                <button type="button" class="text-slate-500 hover:text-teal-600 p-2 rounded-full hover:bg-slate-100"><span class="material-icons-outlined">attach_file</span></button>

                                <input name="message_content" class="flex-1 px-3 py-2.5 rounded-lg border border-slate-300 focus:ring-teal-500 focus:border-teal-500 text-sm" placeholder="Type your message here..." type="text" required/>

                                <button type="submit" class="bg-teal-600 text-white p-2.5 rounded-lg hover:bg-teal-700 transition-colors">
                                    <span class="material-icons-outlined">send</span>
                                </button>
                            </form>
                        </div>
                    </div>
                    <?php else: // If $active_chat_with_user is null (e.g. no conversations at all) ?>
                        <div class="bg-white rounded-xl border border-slate-200 shadow-sm flex flex-col items-center justify-center text-slate-500 p-10">
                            <span class="material-icons-outlined !text-5xl mb-4">chat</span>
                            <p>No active conversation selected.</p>
                            <p class="text-sm">Select a conversation from the list or start a new one.</p>
                        </div>
                    <?php endif; ?>
                </div>
                <?php else: // If there are no conversations and no active user (e.g. new user, no messages at all) ?>
                    <div class="text-center text-slate-500 p-10">
                        <span class="material-icons-outlined !text-6xl mb-4">forum</span>
                        <h2 class="text-xl font-semibold mb-2">No messages yet</h2>
                        <p>You don't have any messages. When you start a conversation, it will appear here.</p>
                    </div>
                <?php
                endif; // End of check for active_chat_with_user or conversations

            } else {
                // For other views, include the specific content file and a generic header.
                $page_title = "Client Portal"; // Default
                $breadcrumb = "";
                switch ($current_view) {
                    case 'dashboard':
                        $page_title = "Dashboard";
                        $breadcrumb = "Dashboard";
                        break;
                    case 'projects':
                        $page_title = "My Projects";
                        $breadcrumb = "My Projects";
                        break;
                    case 'designs_files':
                        $page_title = "Designs & Files";
                        $breadcrumb = "Designs & Files";
                        break;
                    case 'billing':
                        $page_title = "Billing";
                        $breadcrumb = "Billing";
                        break;
                    case 'support':
                        $page_title = "Support";
                        $breadcrumb = "Support";
                        break;
                }
                echo '<header class="mb-6">';
                echo '  <div class="flex flex-col md:flex-row justify-between md:items-center gap-4">';
                echo '    <div>';
                echo '      <div class="flex items-center gap-2 text-sm text-slate-500 mb-1">';
                // Create a breadcrumb link back to the dashboard, then the current page
                echo '        <a href="index.php?page=client_dashboard&view=dashboard" class="hover:text-teal-600">Client Dashboard</a>';
                echo '        <span class="material-icons-outlined !text-sm text-slate-400">chevron_right</span>';
                echo '        <span class="text-slate-700">' . htmlspecialchars($breadcrumb) . '</span>';
                echo '      </div>';
                echo '      <h1 class="text-3xl font-bold text-slate-900">' . htmlspecialchars($page_title) . '</h1>';
                echo '    </div>';
                // Add generic buttons if any, or leave empty for now
                // Example:
                // if ($current_view === 'projects') {
                //    echo '<button class="bg-teal-600 text-white px-4 py-2.5 rounded-lg text-sm font-medium hover:bg-teal-700 transition-colors flex items-center gap-2">';
                //    echo '<span class="material-icons-outlined !text-base">add</span> New Project';
                //    echo '</button>';
                // }
                echo '  </div>';
                echo '</header>';

                $content_file = "templates/client/" . $current_view . "_content.php";
                if (file_exists($content_file)) {
                    include $content_file;
                } else {
                    echo "<p>Error: Content not found for view: " . htmlspecialchars($current_view) . "</p>";
                    echo "<p>Attempted to load: " . htmlspecialchars($content_file) . "</p>";
                }
            }
            ?>
        </main>
    </div>
    <script>
        // Basic mobile menu toggle
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const sidebar = document.getElementById('sidebar');

        if (mobileMenuButton && sidebar) {
            mobileMenuButton.addEventListener('click', () => {
                sidebar.classList.toggle('-translate-x-full');
                sidebar.classList.toggle('translate-x-0'); // Or handle with more specific classes if needed for overlay
            });
        }

        // If you still need the old sidebar toggle for desktop (if it was collapsible)
        // const toggleSidebarBtn = document.getElementById('toggleSidebar'); // Assuming you add this ID to a desktop toggle
        // if (toggleSidebarBtn) {
        //    toggleSidebarBtn.addEventListener('click', () => {
        //        document.querySelector('.sidebar').classList.toggle('collapsed'); // Your existing logic
        //    });
        // }
    </script>
    <!-- script src="../js/script.js" --><!-- Existing script.js might conflict or be redundant -->
</body>
</html>
