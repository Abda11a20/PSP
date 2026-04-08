<!-- Support Chatbot Widget -->
<div id="chatbot-container" style="position: fixed; bottom: 20px; right: 20px; z-index: 1000; font-family: 'Inter', sans-serif;">
    <!-- Chatbot Button -->
    <button id="chatbot-toggle" 
            style="width: 60px; height: 60px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; color: white; font-size: 24px; cursor: pointer; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15); transition: transform 0.2s; display: flex; align-items: center; justify-content: center;">
        <span id="chatbot-icon">💬</span>
        <span id="chatbot-close-icon" style="display: none;">✕</span>
    </button>

    <!-- Chatbot Window -->
    <div id="chatbot-window" 
         style="display: none; width: 380px; height: 564px; background: white; border-radius: 12px; box-shadow: rgba(0, 0, 0, 0.15) 0px 8px 32px; position: absolute; bottom: 60px; right: 0px; flex-direction: column; overflow: hidden;">
        
        <!-- Chatbot Header -->
        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 1rem; display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h3 style="margin: 0; font-size: 1.125rem; font-weight: 600;">Support Chat</h3>
                <p style="margin: 0.25rem 0 0 0; font-size: 0.75rem; opacity: 0.9;">We're here to help!</p>
            </div>
            <button id="chatbot-minimize" style="background: rgba(255, 255, 255, 0.2); border: none; color: white; width: 32px; height: 32px; border-radius: 50%; cursor: pointer; font-size: 1rem;">−</button>
        </div>

        <!-- Chat Messages Area -->
        <div id="chatbot-messages" 
             style="flex: 1; overflow-y: auto; padding: 1rem; background: #f7fafc; display: flex; flex-direction: column; gap: 1rem;">
            
            <!-- Welcome Message -->
            <div class="chatbot-message bot-message" style="display: flex; gap: 0.75rem;">
                <div style="width: 32px; height: 32px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 0.875rem; flex-shrink: 0;">
                    🤖
                </div>
                <div style="flex: 1;">
                    <div style="background: white; padding: 0.75rem 1rem; border-radius: 12px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1); max-width: 85%;">
                        <p style="margin: 0; color: #2d3748; font-size: 0.875rem; line-height: 1.5;">
                            Hello! 👋 I'm your support assistant. How can I help you today?
                        </p>
                    </div>
                    <span style="font-size: 0.75rem; color: #718096; margin-top: 0.25rem; display: block;">Just now</span>
                </div>
            </div>
        </div>

        <!-- Typing Indicator -->
        <div id="chatbot-typing" style="display: none; padding: 0 1rem 0.5rem 1rem;">
            <div style="display: flex; gap: 0.75rem;">
                <div style="width: 32px; height: 32px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 0.875rem; flex-shrink: 0;">
                    🤖
                </div>
                <div style="background: white; padding: 0.75rem 1rem; border-radius: 12px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);">
                    <div style="display: flex; gap: 0.25rem;">
                        <div style="width: 8px; height: 8px; border-radius: 50%; background: #667eea; animation: typing 1.4s infinite;"></div>
                        <div style="width: 8px; height: 8px; border-radius: 50%; background: #667eea; animation: typing 1.4s infinite 0.2s;"></div>
                        <div style="width: 8px; height: 8px; border-radius: 50%; background: #667eea; animation: typing 1.4s infinite 0.4s;"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chat Input Area -->
        <div style="padding: 1rem; background: white; border-top: 1px solid #e2e8f0;">
            <form id="chatbot-form" style="display: flex; gap: 0.5rem;">
                <input type="text" 
                       id="chatbot-input" 
                       placeholder="Type your message..." 
                       style="flex: 1; padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.875rem; outline: none; transition: border-color 0.2s;"
                       onfocus="this.style.borderColor='#667eea'"
                       onblur="this.style.borderColor='#e2e8f0'">
                <button type="submit" 
                        id="chatbot-send"
                        style="padding: 0.75rem 1.25rem; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 0.875rem; font-weight: 600; transition: opacity 0.2s;"
                        onmouseover="this.style.opacity='0.9'"
                        onmouseout="this.style.opacity='1'">
                    Send
                </button>
            </form>
            <p style="margin: 0.5rem 0 0 0; font-size: 0.75rem; color: #718096; text-align: center;">
                Powered by Support Team
            </p>
        </div>
    </div>
</div>

<style>
    @keyframes typing {
        0%, 60%, 100% {
            transform: translateY(0);
            opacity: 0.7;
        }
        30% {
            transform: translateY(-10px);
            opacity: 1;
        }
    }

    #chatbot-messages::-webkit-scrollbar {
        width: 6px;
    }

    #chatbot-messages::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    #chatbot-messages::-webkit-scrollbar-thumb {
        background: #cbd5e0;
        border-radius: 3px;
    }

    #chatbot-messages::-webkit-scrollbar-thumb:hover {
        background: #a0aec0;
    }

    .chatbot-message {
        animation: fadeIn 0.3s ease-in;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @media (max-width: 480px) {
        #chatbot-container {
            bottom: 10px;
            right: 10px;
            left: 10px;
        }
        
        #chatbot-window {
            width: calc(100vw - 20px);
            max-width: 380px;
            height: calc(100vh - 100px);
            max-height: 564px;
            right: 0;
            left: auto;
            bottom: 70px;
        }
        
        #chatbot-toggle {
            width: 50px;
            height: 50px;
            font-size: 20px;
        }
    }
    
    @media (max-width: 360px) {
        #chatbot-window {
            width: calc(100vw - 20px);
            height: calc(100vh - 80px);
            max-height: 500px;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const chatbotToggle = document.getElementById('chatbot-toggle');
    const chatbotWindow = document.getElementById('chatbot-window');
    const chatbotIcon = document.getElementById('chatbot-icon');
    const chatbotCloseIcon = document.getElementById('chatbot-close-icon');
    const chatbotMinimize = document.getElementById('chatbot-minimize');
    const chatbotForm = document.getElementById('chatbot-form');
    const chatbotInput = document.getElementById('chatbot-input');
    const chatbotMessages = document.getElementById('chatbot-messages');
    const chatbotTyping = document.getElementById('chatbot-typing');
    const chatbotSend = document.getElementById('chatbot-send');

    let isOpen = false;

    // Toggle chatbot
    chatbotToggle.addEventListener('click', function() {
        if (isOpen) {
            closeChatbot();
        } else {
            openChatbot();
        }
    });

    // Minimize chatbot
    chatbotMinimize.addEventListener('click', function() {
        closeChatbot();
    });

    function openChatbot() {
        chatbotWindow.style.display = 'flex';
        chatbotWindow.style.flexDirection = 'column';
        chatbotIcon.style.display = 'none';
        chatbotCloseIcon.style.display = 'block';
        isOpen = true;
        chatbotInput.focus();
    }

    function closeChatbot() {
        chatbotWindow.style.display = 'none';
        chatbotIcon.style.display = 'block';
        chatbotCloseIcon.style.display = 'none';
        isOpen = false;
    }

    // Handle form submission
    chatbotForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const message = chatbotInput.value.trim();
        
        if (!message) return;

        // Add user message
        addMessage(message, 'user');
        chatbotInput.value = '';
        
        // Show typing indicator
        showTyping();
        
        // Disable input
        chatbotInput.disabled = true;
        chatbotSend.disabled = true;

        // Send message to server
        fetch('{{ route("chatbot.message") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({ message: message })
        })
        .then(response => response.json())
        .then(data => {
            hideTyping();
            if (data.success) {
                addMessage(data.response, 'bot');
            } else {
                addMessage('Sorry, I encountered an error. Please try again or contact support directly.', 'bot');
            }
        })
        .catch(error => {
            hideTyping();
            addMessage('Sorry, I couldn\'t connect to the server. Please try again later or contact support directly.', 'bot');
        })
        .finally(() => {
            chatbotInput.disabled = false;
            chatbotSend.disabled = false;
            chatbotInput.focus();
        });
    });

    function addMessage(text, type) {
        const messageDiv = document.createElement('div');
        messageDiv.className = 'chatbot-message ' + (type === 'user' ? 'user-message' : 'bot-message');
        
        const timestamp = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        
        if (type === 'user') {
            messageDiv.style.cssText = 'display: flex; gap: 0.75rem; justify-content: flex-end;';
            messageDiv.innerHTML = `
                <div style="flex: 1; display: flex; flex-direction: column; align-items: flex-end;">
                    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 0.75rem 1rem; border-radius: 12px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1); max-width: 85%;">
                        <p style="margin: 0; font-size: 0.875rem; line-height: 1.5; white-space: pre-wrap;">${escapeHtml(text)}</p>
                    </div>
                    <span style="font-size: 0.75rem; color: #718096; margin-top: 0.25rem;">${timestamp}</span>
                </div>
                <div style="width: 32px; height: 32px; border-radius: 50%; background: #e2e8f0; display: flex; align-items: center; justify-content: center; color: #4a5568; font-size: 0.875rem; flex-shrink: 0;">
                    👤
                </div>
            `;
        } else {
            messageDiv.style.cssText = 'display: flex; gap: 0.75rem;';
            messageDiv.innerHTML = `
                <div style="width: 32px; height: 32px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 0.875rem; flex-shrink: 0;">
                    🤖
                </div>
                <div style="flex: 1;">
                    <div style="background: white; padding: 0.75rem 1rem; border-radius: 12px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1); max-width: 85%;">
                        <p style="margin: 0; color: #2d3748; font-size: 0.875rem; line-height: 1.5; white-space: pre-wrap;">${formatBotMessage(text)}</p>
                    </div>
                    <span style="font-size: 0.75rem; color: #718096; margin-top: 0.25rem; display: block;">${timestamp}</span>
                </div>
            `;
        }
        
        chatbotMessages.appendChild(messageDiv);
        chatbotMessages.scrollTop = chatbotMessages.scrollHeight;
    }

    function showTyping() {
        chatbotTyping.style.display = 'block';
        chatbotMessages.scrollTop = chatbotMessages.scrollHeight;
    }

    function hideTyping() {
        chatbotTyping.style.display = 'none';
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function formatBotMessage(text) {
        // Use placeholder to protect links during HTML escaping
        const linkPlaceholders = [];
        let placeholderIndex = 0;
        
        // Convert markdown-style links [text](url) to HTML links
        text = text.replace(/\[([^\]]+)\]\(([^)]+)\)/g, function(match, linkText, url) {
            const placeholder = `___LINK_${placeholderIndex}___`;
            linkPlaceholders[placeholderIndex] = '<a href="' + escapeHtml(url) + '" style="color: #2b6cb0; text-decoration: underline;" target="_blank">' + escapeHtml(linkText) + '</a>';
            placeholderIndex++;
            return placeholder;
        });
        
        // Convert plain URLs to clickable links (before escaping)
        text = text.replace(/(https?:\/\/[^\s<>"']+)/g, function(url) {
            const placeholder = `___URL_${placeholderIndex}___`;
            linkPlaceholders[placeholderIndex] = '<a href="' + escapeHtml(url) + '" style="color: #2b6cb0; text-decoration: underline;" target="_blank">' + escapeHtml(url) + '</a>';
            placeholderIndex++;
            return placeholder;
        });
        
        // Escape HTML to prevent XSS
        text = escapeHtml(text);
        
        // Restore all links (both markdown and plain URLs)
        linkPlaceholders.forEach((link, index) => {
            text = text.replace(`___LINK_${index}___`, link);
            text = text.replace(`___URL_${index}___`, link);
        });
        
        // Convert newlines to <br>
        text = text.replace(/\n/g, '<br>');
        
        return text;
    }
});
</script>

