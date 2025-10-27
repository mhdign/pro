// components/LoadingLogo.jsx
import React from "react";
import { motion, AnimatePresence } from "framer-motion";

export default function LoadingLogo({ visible = false, text = "Processing..." }) {
    return (
        <AnimatePresence>
            {visible && (
                <motion.div
                    initial={{ opacity: 0 }}
                    animate={{ opacity: 1 }}
                    exit={{ opacity: 0 }}
                    transition={{ duration: 0.3, ease: "easeInOut" }}
                    className="fixed inset-0 z-50 flex items-center justify-center pointer-events-none"
                >
                    {/* Backdrop */}
                    <motion.div
                        initial={{ opacity: 0 }}
                        animate={{ opacity: 1 }}
                        exit={{ opacity: 0 }}
                        className="absolute inset-0 bg-black/20 dark:bg-white/10 backdrop-blur-sm"
                    />
                    
                    {/* Loading Card */}
                    <motion.div
                        initial={{ opacity: 0, scale: 0.8, y: 20 }}
                        animate={{ opacity: 1, scale: 1, y: 0 }}
                        exit={{ opacity: 0, scale: 0.8, y: 20 }}
                        transition={{ duration: 0.4, ease: "easeOut" }}
                        className="relative bg-white/95 dark:bg-slate-800/95 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/20 dark:border-slate-700/20 p-8 flex flex-col items-center gap-6 min-w-[280px]"
                    >
                        {/* Modern Spinner */}
                        <div className="relative w-20 h-20">
                            {/* Outer Ring */}
                            <motion.div
                                className="absolute inset-0 border-4 border-slate-200 dark:border-slate-700 rounded-full"
                                animate={{ rotate: 360 }}
                                transition={{
                                    duration: 2,
                                    repeat: Infinity,
                                    ease: "linear"
                                }}
                            />
                            
                            {/* Inner Spinning Ring */}
                            <motion.div
                                className="absolute inset-0 border-4 border-transparent border-t-indigo-500 border-r-purple-500 rounded-full"
                                animate={{ rotate: 360 }}
                                transition={{
                                    duration: 1,
                                    repeat: Infinity,
                                    ease: "easeInOut"
                                }}
                            />
                            
                            {/* Center Dot */}
                            <motion.div
                                className="absolute top-1/2 left-1/2 w-3 h-3 bg-gradient-to-r from-indigo-500 to-purple-500 rounded-full transform -translate-x-1/2 -translate-y-1/2"
                                animate={{ 
                                    scale: [1, 1.2, 1],
                                    opacity: [0.7, 1, 0.7]
                                }}
                                transition={{
                                    duration: 1.5,
                                    repeat: Infinity,
                                    ease: "easeInOut"
                                }}
                            />
                        </div>
                        
                        {/* Loading Text */}
                        <motion.div
                            initial={{ opacity: 0, y: 10 }}
                            animate={{ opacity: 1, y: 0 }}
                            transition={{ delay: 0.2, duration: 0.3 }}
                            className="text-center"
                        >
                            <p className="text-lg font-medium text-slate-800 dark:text-slate-100 mb-1">
                                {text}
                            </p>
                            <div className="flex items-center justify-center gap-1">
                                <motion.div
                                    className="w-2 h-2 bg-indigo-500 rounded-full"
                                    animate={{ 
                                        scale: [1, 1.2, 1],
                                        opacity: [0.5, 1, 0.5]
                                    }}
                                    transition={{
                                        duration: 1,
                                        repeat: Infinity,
                                        delay: 0
                                    }}
                                />
                                <motion.div
                                    className="w-2 h-2 bg-purple-500 rounded-full"
                                    animate={{ 
                                        scale: [1, 1.2, 1],
                                        opacity: [0.5, 1, 0.5]
                                    }}
                                    transition={{
                                        duration: 1,
                                        repeat: Infinity,
                                        delay: 0.2
                                    }}
                                />
                                <motion.div
                                    className="w-2 h-2 bg-indigo-500 rounded-full"
                                    animate={{ 
                                        scale: [1, 1.2, 1],
                                        opacity: [0.5, 1, 0.5]
                                    }}
                                    transition={{
                                        duration: 1,
                                        repeat: Infinity,
                                        delay: 0.4
                                    }}
                                />
                            </div>
                        </motion.div>
                        
                        {/* Progress Bar */}
                        <motion.div
                            initial={{ opacity: 0 }}
                            animate={{ opacity: 1 }}
                            transition={{ delay: 0.4, duration: 0.3 }}
                            className="w-full max-w-[200px] h-1 bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden"
                        >
                            <motion.div
                                className="h-full bg-gradient-to-r from-indigo-500 to-purple-500 rounded-full"
                                initial={{ width: "0%" }}
                                animate={{ width: "100%" }}
                                transition={{
                                    duration: 2,
                                    repeat: Infinity,
                                    ease: "easeInOut"
                                }}
                            />
                        </motion.div>
                    </motion.div>
                </motion.div>
            )}
        </AnimatePresence>
    );
}
