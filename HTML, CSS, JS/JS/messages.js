document.addEventListener('DOMContentLoaded', function() {
    const messagesContainer = document.getElementById('messages-container');
    const messageInput = document.getElementById('message-input');
    const sendButton = document.getElementById('send-message');
    const chatHeader = document.getElementById('chat-header');
    const messageInputContainer = document.querySelector('.message-input-container');
    const contactSearch = document.getElementById('contact-search');
    
    let selectedContact = null;
    let lastMessageTime = null;
    let polling;

    const userType = document.body.dataset.userType;
    const userId = document.body.dataset.userId;
    const newChatModal = userType === 'employer' ? new bootstrap.Modal(document.getElementById('newChatModal')) : null;

    // Format timestamp
    function formatTimestamp(timestamp) {
        const date = new Date(timestamp);
        const now = new Date();
        const isToday = date.toDateString() === now.toDateString();
        const isYesterday = new Date(now - 86400000).toDateString() === date.toDateString();
        
        if (isToday) {
            return date.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' });
        } else if (isYesterday) {
            return 'Yesterday ' + date.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' });
        } else {
            return date.toLocaleDateString('en-US', { 
                month: 'short', 
                day: 'numeric',
                hour: 'numeric',
                minute: '2-digit'
            });
        }
    }

    // Setup applicant item click handlers
    function setupApplicantHandlers() {
        document.querySelectorAll('.applicant-item').forEach(item => {
            if (!item.dataset.hasHandler) {
                item.addEventListener('click', function(e) {
                    // Don't trigger if clicking the start chat button
                    if (e.target.closest('.start-chat-btn')) return;
                    
                    document.querySelectorAll('.applicant-item').forEach(i => i.classList.remove('active'));
                    this.classList.add('active');
                    
                    selectedContact = {
                        contact_id: this.dataset.userId,
                        contact_name: this.dataset.userName
                    };
                    
                    loadMessages(selectedContact);
                    messageInputContainer.style.display = 'flex';
                    chatHeader.innerHTML = `<h3>${selectedContact.contact_name}</h3>`;
                });
                item.dataset.hasHandler = 'true';
            }
        });
    }

    // Setup employer conversation click handlers for users
    function setupEmployerConversationHandlers() {
        document.querySelectorAll('.employer-item').forEach(item => {
            if (!item.dataset.hasHandler) {
                item.addEventListener('click', function() {
                    document.querySelectorAll('.employer-item').forEach(i => i.classList.remove('active'));
                    this.classList.add('active');
                    
                    selectedContact = {
                        contact_id: this.dataset.userId,  // Using data-user-id for consistency
                        contact_name: this.dataset.userName  // Using data-user-name for consistency
                    };
                    
                    // Show message input when conversation is selected
                    messageInputContainer.style.display = 'flex';
                    chatHeader.innerHTML = `<h3>${selectedContact.contact_name}</h3>`;
                    
                    // Load messages immediately
                    loadMessages(selectedContact);
                });
                item.dataset.hasHandler = 'true';
            }
        });
    }

    // Filter contacts/applicants
    function filterContacts(searchText) {
        const items = document.querySelectorAll('.contact-item, .applicant-item');
        const searchLower = searchText.toLowerCase();
        
        items.forEach(item => {
            const name = item.querySelector('.contact-name').textContent.toLowerCase();
            const email = item.querySelector('.contact-email')?.textContent.toLowerCase() || '';
            const jobTitle = item.querySelector('.job-applied')?.textContent.toLowerCase() || '';
            
            if (name.includes(searchLower) || 
                email.includes(searchLower) || 
                jobTitle.includes(searchLower)) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    }

    // Start new chat (for recruiters)
    window.startNewChat = function(btn) {
        const item = btn.closest('.applicant-item');
        document.getElementById('recipientName').textContent = item.dataset.userName;
        selectedContact = {
            contact_id: item.dataset.userId,
            contact_name: item.dataset.userName
        };
        newChatModal.show();
    };

    // Send first message
    window.sendFirstMessage = function() {
        const message = document.getElementById('newMessageText').value.trim();
        if (!message || !selectedContact) return;

        fetch('../PHP/api/start-conversation.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                user_id: selectedContact.contact_id,
                message: message
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                newChatModal.hide();
                document.getElementById('newMessageText').value = '';
                // Remove start chat button and show messages
                const applicantItem = document.querySelector(`.applicant-item[data-user-id="${selectedContact.contact_id}"]`);
                const startChatBtn = applicantItem.querySelector('.start-chat-btn');
                if (startChatBtn) startChatBtn.remove();
                
                // Load the messages
                loadMessages(selectedContact);
                messageInputContainer.style.display = 'flex';
                chatHeader.innerHTML = `<h3>${selectedContact.contact_name}</h3>`;
            } else {
                throw new Error(data.error || 'Failed to send message');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert(error.message || 'An error occurred while sending the message');
        });
    };

    // Load messages for a contact
    function loadMessages(contact) {
        console.log('Loading messages for contact:', contact); // Debug log
        
        fetch(`../PHP/api/get-messages.php?contact_id=${contact.contact_id}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success && data.messages) {
                    messagesContainer.innerHTML = '';
                    let lastDate = '';
                    
                    data.messages.forEach(message => {
                        const messageDate = new Date(message.created_at).toDateString();
                        if (messageDate !== lastDate) {
                            const dateDiv = document.createElement('div');
                            dateDiv.className = 'message-date';
                            dateDiv.textContent = messageDate;
                            messagesContainer.appendChild(dateDiv);
                            lastDate = messageDate;
                        }

                        const messageDiv = document.createElement('div');
                        messageDiv.className = `message ${message.sender_id == userId ? 'sent' : 'received'}`;
                        messageDiv.innerHTML = `
                            <div class="message-content">${message.message_text}</div>
                            <div class="message-timestamp">${formatTimestamp(message.created_at)}</div>
                        `;
                        messagesContainer.appendChild(messageDiv);
                    });
                    
                    messagesContainer.scrollTop = messagesContainer.scrollHeight;
                    lastMessageTime = data.messages.length > 0 
                        ? data.messages[data.messages.length - 1].created_at 
                        : null;

                    // Update unread badge for the correct conversation
                    const contactItem = userType === 'employer' 
                        ? document.querySelector(`.applicant-item[data-user-id="${contact.contact_id}"]`)
                        : document.querySelector(`.employer-item[data-user-id="${contact.contact_id}"]`);
                    const unreadBadge = contactItem?.querySelector('.unread-badge');
                    if (unreadBadge) unreadBadge.remove();
                } else if (data.error) {
                    throw new Error(data.error);
                }
            })
            .catch(error => {
                console.error('Error loading messages:', error);
                alert(error.message || 'An error occurred while loading messages');
            });
    }

    // Send message
    function sendMessage() {
        if (!selectedContact || !messageInput.value.trim()) return;

        const messageData = {
            receiver_id: selectedContact.contact_id,
            message: messageInput.value.trim()
        };

        console.log('Sending message:', messageData); // Debug log

        fetch('../PHP/api/send-message.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(messageData)
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                messageInput.value = '';
                loadMessages(selectedContact);
            } else {
                throw new Error(data.error || 'Failed to send message');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert(error.message || 'An error occurred while sending the message');
        });
    }

    // Event listeners
    sendButton.addEventListener('click', sendMessage);
    messageInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });

    contactSearch.addEventListener('input', (e) => {
        filterContacts(e.target.value);
    });

    // Initialize
    if (userType === 'employer') {
        setupApplicantHandlers();
    } else {
        setupEmployerConversationHandlers();
    }

    // Start polling for updates
    setInterval(() => {
        if (selectedContact) {
            loadMessages(selectedContact);
        }
    }, 5000);
});
