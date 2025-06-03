-- includes/sql/schema.sql

-- Drop tables if they exist (optional, for easy re-creation during development)
DROP TABLE IF EXISTS messages;
DROP TABLE IF EXISTS users;

-- Users table
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL, -- Store hashed passwords, not plain text
    email VARCHAR(100) NOT NULL UNIQUE,
    full_name VARCHAR(100),
    role VARCHAR(50), -- e.g., 'client', 'architect', 'designer', 'admin'
    avatar_url VARCHAR(255), -- URL to profile picture
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Messages table
CREATE TABLE messages (
    message_id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    project_id INT NULL, -- Optional: if messages are related to specific projects
    message_content TEXT NOT NULL,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_read BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (sender_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES users(user_id) ON DELETE CASCADE
    -- FOREIGN KEY (project_id) REFERENCES projects(project_id) -- If you have a projects table
);

-- Sample data for users table
INSERT INTO users (user_id, username, password_hash, email, full_name, role, avatar_url) VALUES
(1, 'eleanorv', 'hashed_password_1', 'eleanor.vance@example.com', 'Eleanor Vance', 'client', 'https://lh3.googleusercontent.com/aida-public/AB6AXuA4vR8P49C6vjBeZyiXHsnkny2si4BhvnkZZVplFCtidUa4x0XhppxqmMJGb7yRq_WlPvb2LZUFqv4ZabT79g5RzpMm1LTWqAeqfSKLN8YOXWdxU1NkCWu48NaDJtcqcvQRSC9hCAYJL9Qs4yHvpIBVu_MXdF0ROCv3yIhVCFzIlqpcVg-jIt5IYY-xL7roeMduwW6vXwgt7KMnjLImcR2wARZXybBBope2h9pasDii34_7IEk2IsKTl999b53sHSlBzP7-4oT3HGs'), -- Placeholder avatar for Eleanor
(2, 'marcusf', 'hashed_password_2', 'marcus.finch@example.com', 'Marcus Finch', 'Lead Architect', 'https://lh3.googleusercontent.com/aida-public/AB6AXuDWrhvIkBH5uT9kqJYTOwf_3kBTTHJJnglNVqRnXctjX17w9mr-RJ5AvSTDb7fLGxnudv6pkeLWds99MewwwGPtDMM-pHexPGvd3hNTyYPc1vYhAYdkDy5TinZOBdrZS2aPdTvHfCGq4eLO5grEJGoMHvF7uDAqiPtEH7YuZgEo5ioosLLz6-sLUfMnGJdvIOkieDa1969tNKtFE9OuAFWtv-Iv1ozQ4idQ6JcA2tgYZpz6yGTKDeVg1mjcKZNoLqhFHHbgRQnqzJY'),
(3, 'ameliac', 'hashed_password_3', 'amelia.chen@example.com', 'Amelia Chen', 'Draftsman', 'https://lh3.googleusercontent.com/aida-public/AB6AXuAFY0ZApb8wrPdn-RhAxqDe-bThkVNh60sc08ZYHDeUPB4GJtzQlejUzafa8N_Hg8k9DTC5_6vTaI_GhZuq23X_tq2chlBumc7GWewYbOaACMKEJBGIUQemStxsq501sJ9MmeCOpLgwTPT7MsGIaoBK0q57xZd1COtMyOkLOzLJZKSm_nNp72TNpLlLgEb2jGBArmOQW5jZBSiBkFj7xx2MxqhzvB_vtkPpIIp7YAhTZKCpCJ-5WGY_IcUzqmR736NFQutw6NlUWJw'),
(4, 'support', 'hashed_password_4', 'support@archiaxis.com', 'ArchiAxis Support', 'Support', 'https://lh3.googleusercontent.com/aida-public/AB6AXuDLBPFDhn1qagk7kcvgFYtXs19C4PDvtfvEWYR0c3IOZkDlTt2vbaWm_TiuouFFupNQmRtDfU87wQNO0eKEdbIxir6SMmnXAb_767VeUSxuiRp0MPLQOwBk267Do45C--8rnvpgDzspeHzxI3tKbDN_aae-CpmMCBnfPlKRhk9abT7eGOBaIRwNLh9OxuGz-SVoTjVMjIdUcc8ziLquqfZsXFQSdbH2gysUEpjgEBHgFuXj0bX0LR486lQGGo7uZLvzRYm2s6F0B4c');

-- Sample messages (Eleanor Vance is user_id 1)
-- Conversation between Eleanor (1) and Marcus (2)
INSERT INTO messages (sender_id, receiver_id, message_content, is_read) VALUES
(2, 1, 'Hey Eleanor, I''ve just uploaded the initial sketches for the Downtown Apartment. Let me know your thoughts when you get a chance!', TRUE),
(1, 2, 'Hi Marcus, thanks! I''ll take a look this afternoon and get back to you with feedback.', FALSE),
(2, 1, 'Sounds good. No rush!', FALSE);

-- Conversation between Eleanor (1) and Amelia (3)
INSERT INTO messages (sender_id, receiver_id, message_content, is_read) VALUES
(3, 1, 'Hi Eleanor, I''ve sent the revised 3D model for the kitchen. Please check if it meets your requirements.', TRUE),
(1, 3, 'Thanks Amelia, looking at it now. Will let you know if there are any changes.', FALSE);

-- Conversation between Eleanor (1) and Support (4)
INSERT INTO messages (sender_id, receiver_id, message_content, is_read) VALUES
(4, 1, 'Welcome to your new project dashboard, Eleanor! Let us know if you need any help navigating the platform.', TRUE),
(1, 4, 'Thank you! Everything looks great so far.', FALSE);

-- More messages for Marcus (2) to Eleanor (1) to make the conversation longer
INSERT INTO messages (sender_id, receiver_id, message_content, is_read) VALUES
(2, 1, 'Also, regarding the material selection for the facade, do you have any initial preferences?', TRUE),
(1, 2, 'I was thinking something modern, perhaps a mix of glass and steel. But open to your suggestions.', FALSE),
(2, 1, 'Great, that aligns with our initial concepts. I''ll prepare a mood board with some options.', FALSE);

-- Unread message for Eleanor (1) from Marcus (2)
INSERT INTO messages (sender_id, receiver_id, message_content, is_read) VALUES
(2, 1, 'Quick question about the floor plan dimensions for the master bedroom - can we schedule a brief call tomorrow?', FALSE);

-- Message from Amelia (3) to Eleanor (1)
INSERT INTO messages (sender_id, receiver_id, message_content, is_read) VALUES
(3, 1, 'Just a reminder about the deadline for the color palette selection by end of this week. Let me know if you need more time.', TRUE);

-- Conversation between Marcus (2) and Support (4)
INSERT INTO messages (sender_id, receiver_id, message_content, is_read) VALUES
(2, 4, 'Hi Support, I have a question about the new project submission guidelines.', FALSE),
(4, 2, 'Hello Marcus, sure, what''s your question?', TRUE),
(2, 4, 'Are there any specific file formats required for the initial concept documents?', FALSE);

-- Ensure timestamps are varied if you need to test ordering by time (CURRENT_TIMESTAMP handles this for new inserts)
-- For existing data, you might need to manually set timestamps if specific order is critical for tests.
-- For example:
-- UPDATE messages SET timestamp = DATE_SUB(NOW(), INTERVAL 2 HOUR) WHERE message_id = 1;
-- UPDATE messages SET timestamp = DATE_SUB(NOW(), INTERVAL 1 HOUR) WHERE message_id = 2;
-- etc.
-- However, for this setup, default CURRENT_TIMESTAMP should be sufficient for basic ordering.

CREATE INDEX idx_sender_receiver ON messages(sender_id, receiver_id);
CREATE INDEX idx_receiver_sender ON messages(receiver_id, sender_id);
CREATE INDEX idx_is_read ON messages(is_read);

-- Note: Password hashing should be done in PHP (e.g., password_hash()) before inserting into users table.
-- The 'hashed_password_X' are placeholders.
-- In a real application, you would not store default passwords like this directly in SQL. Users would be created via a registration form.
