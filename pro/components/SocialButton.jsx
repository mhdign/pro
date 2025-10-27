// components/SocialButton.jsx
import React from "react";
import { motion } from "framer-motion";

export default function SocialButton({ 
    onClick, 
    children, 
    className = "", 
    icon,
    disabled = false 
}) {
    return (
        <motion.button
            type="button"
            onClick={onClick}
            disabled={disabled}
            whileHover={{ scale: 1.02 }}
            whileTap={{ scale: 0.98 }}
            className={`
                flex items-center justify-center gap-3 px-4 py-3 rounded-2xl border-2 
                transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-offset-2 
                disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100
                hover:shadow-lg active:shadow-md
                ${className}
            `}
            style={{
                // Prevent layout shift by maintaining consistent dimensions
                minHeight: '48px',
                lineHeight: '1',
            }}
        >
            <span className="w-5 h-5 flex items-center justify-center flex-shrink-0">
                {icon}
            </span>
            <span className="flex-1 text-sm font-medium text-center">
                {children}
            </span>
        </motion.button>
    );
}
