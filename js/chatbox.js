// Floating Chatbox JavaScript
document.addEventListener('DOMContentLoaded', function() {
    const chatBubble = document.getElementById('chatBubble');
    const chatWindow = document.getElementById('chatWindow');
    const chatClose = document.getElementById('chatClose');
    const chatInput = document.getElementById('chatInput');
    const chatSend = document.getElementById('chatSend');
    const chatMessages = document.getElementById('chatMessages');

    // Load user info from localStorage
    let userInfo = {
        name: localStorage.getItem('chatUserName') || '',
        email: localStorage.getItem('chatUserEmail') || ''
    };

    // Open chat
    chatBubble.addEventListener('click', function() {
        chatWindow.classList.add('active');
        chatBubble.style.display = 'none';
        chatInput.focus();
        
        // Ask for name and email if not provided
        if(!userInfo.name){
            askUserInfo();
        }
       
           // Load chat history if email exists
           if(userInfo.email){
               loadChatHistory();
           }
    });

    // Close chat
    chatClose.addEventListener('click', function() {
        chatWindow.classList.remove('active');
        chatBubble.style.display = 'flex';
    });

    // Ask for user info
    function askUserInfo() {
        const name = prompt('Vui lòng nhập tên của bạn:');
        if(name){
            userInfo.name = name;
            localStorage.setItem('chatUserName', name);
        }
        
        const email = prompt('Vui lòng nhập email của bạn:');
        if(email){
            userInfo.email = email;
            localStorage.setItem('chatUserEmail', email);
        }
    }

       // Load chat history
       function loadChatHistory() {
           fetch('ajax_get_chat_history.php', {
               method: 'POST',
               headers: {
                   'Content-Type': 'application/x-www-form-urlencoded',
               },
               body: 'email=' + encodeURIComponent(userInfo.email)
           })
           .then(response => response.json())
           .then(data => {
               // Clear existing messages
               chatMessages.innerHTML = '';
           
               // Add historical messages
               if(data.messages && data.messages.length > 0){
                   data.messages.forEach(msg => {
                       const type = msg.sender_type === 'customer' ? 'user' : 'bot';
                       addMessage(msg.message, type);
                   });
               } else {
                   addMessage('Chào bạn! Chúng tôi có thể giúp gì cho bạn?', 'bot');
               }
           })
           .catch(error => {
               console.error('Error loading history:', error);
               addMessage('Chào bạn! Chúng tôi có thể giúp gì cho bạn?', 'bot');
           });
       }

    // Send message
    function sendMessage() {
        const message = chatInput.value.trim();
        if (message === '') return;

        // Ask for user info if not provided
        if(!userInfo.name || !userInfo.email){
            askUserInfo();
        }

        // Add user message
        addMessage(message, 'user');
        chatInput.value = '';

        // Send to server
        fetch('ajax_chat_message.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'message=' + encodeURIComponent(message) + 
                  '&sender_name=' + encodeURIComponent(userInfo.name) + 
                  '&sender_email=' + encodeURIComponent(userInfo.email)
        })
        .then(response => response.json())
        .then(data => {
            // Add bot response
            if (data.reply) {
                setTimeout(() => {
                    addMessage(data.reply, 'bot');
                }, 500);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            addMessage('Xin lỗi, có lỗi xảy ra. Vui lòng thử lại sau.', 'bot');
        });
    }

    // Add message to chat
    function addMessage(text, type) {
        const messageDiv = document.createElement('div');
        messageDiv.className = 'chat-message';
        
        const msgText = document.createElement('div');
        msgText.className = 'message-' + type;
        msgText.textContent = text;
        
        messageDiv.appendChild(msgText);
        chatMessages.appendChild(messageDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    // Send on button click
    chatSend.addEventListener('click', sendMessage);

    // Send on Enter key
    chatInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            sendMessage();
        }
    });

    // Auto-close chat when clicking outside (optional)
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.floating-chat')) {
            // Uncomment below to close chat when clicking outside
            // chatWindow.classList.remove('active');
            // chatBubble.style.display = 'flex';
        }
    });
});

