import { ref } from 'vue'

const notifications = ref([])

export function useNotification() {
    const showNotification = (type, title, message = '', duration = 5000) => {
        const id = Date.now()
        const notification = {
            id,
            type,
            title,
            message,
            show: true
        }
        
        notifications.value.push(notification)
        
        // Auto close after duration
        setTimeout(() => {
            closeNotification(id)
        }, duration)
        
        return id
    }
    
    const closeNotification = (id) => {
        const index = notifications.value.findIndex(n => n.id === id)
        if (index > -1) {
            notifications.value.splice(index, 1)
        }
    }
    
    const showSuccess = (title, message = '', duration = 4000) => {
        return showNotification('success', title, message, duration)
    }
    
    const showError = (title, message = '', duration = 6000) => {
        return showNotification('error', title, message, duration)
    }
    
    const showWarning = (title, message = '', duration = 5000) => {
        return showNotification('warning', title, message, duration)
    }
    
    const showInfo = (title, message = '', duration = 4000) => {
        return showNotification('info', title, message, duration)
    }
    
    return {
        notifications,
        showNotification,
        closeNotification,
        showSuccess,
        showError,
        showWarning,
        showInfo
    }
}
