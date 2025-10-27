// pages/login.jsx
import React, { useState } from "react";
import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { z } from "zod";
import axios from "axios";
import { motion, AnimatePresence } from "framer-motion";
import LoadingLogo from "../components/LoadingLogo";
import SocialButton from "../components/SocialButton";
import { FcGoogle } from "react-icons/fc";
import { SiMicrosoft } from "react-icons/si";
import { useRouter } from "next/router";
import { Eye, EyeOff, Mail, Lock } from "lucide-react";

// Zod validation schema for modern validation
const loginSchema = z.object({
    email: z
        .string()
        .min(1, "ایمیل الزامی است")
        .email("فرمت ایمیل نامعتبر است"),
    password: z
        .string()
        .min(6, "رمز عبور باید حداقل 6 کاراکتر باشد")
        .max(100, "رمز عبور نمی‌تواند بیش از 100 کاراکتر باشد"),
});

export default function LoginPage() {
    const router = useRouter();
    const [loading, setLoading] = useState(false);
    const [serverError, setServerError] = useState("");
    const [showPassword, setShowPassword] = useState(false);
    
    const { 
        register, 
        handleSubmit, 
        formState: { errors, isSubmitting },
        watch 
    } = useForm({
        resolver: zodResolver(loginSchema),
        mode: "onChange", // Real-time validation
    });

    // Watch form values for dynamic validation feedback
    const watchedValues = watch();

    async function onSubmit(data) {
        setServerError("");
        setLoading(true);
        
        try {
            const startTime = Date.now();
            const res = await axios.post("/api/auth/login", data, { 
                timeout: 5000, // Reduced timeout for faster feedback
                headers: {
                    'Content-Type': 'application/json',
                }
            });
            
            if (res?.data?.ok) {
                // Store token securely
                if (res.data.token) {
                    localStorage.setItem("token", res.data.token);
                }
                
                // Ultra-fast redirect with smooth animation
                const elapsed = Date.now() - startTime;
                const remainingTime = Math.max(200, 1000 - elapsed); // Ensure minimum 200ms for UX
                
                setTimeout(() => {
                    router.replace("/home");
                }, remainingTime);
            } else {
                setServerError(res.data?.message || "خطا در ورود");
                setLoading(false);
            }
        } catch (err) {
            setServerError(
                err?.response?.data?.message || 
                "خطا در اتصال به سرور. لطفاً دوباره تلاش کنید."
            );
            setLoading(false);
        }
    }

    return (
        <div className="min-h-screen flex items-center justify-center bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 dark:from-slate-900 dark:via-slate-800 dark:to-indigo-900 transition-all duration-500">
            <LoadingLogo visible={loading} text="در حال ورود..." />
            
            <motion.div 
                initial={{ opacity: 0, y: 20 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.6, ease: "easeOut" }}
                className="w-full max-w-md mx-4"
            >
                <div className="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/20 dark:border-slate-700/20 p-8">
                    {/* Header */}
                    <motion.div 
                        initial={{ opacity: 0, y: -10 }}
                        animate={{ opacity: 1, y: 0 }}
                        transition={{ delay: 0.2, duration: 0.5 }}
                        className="text-center mb-8"
                    >
                        <h1 className="text-3xl font-bold text-slate-800 dark:text-slate-100 mb-2">
                            ورود
                        </h1>
                        <p className="text-slate-600 dark:text-slate-400">
                            به حساب کاربری خود وارد شوید
                        </p>
                    </motion.div>

                    <form onSubmit={handleSubmit(onSubmit)} className="space-y-6">
                        {/* Email Field */}
                        <motion.div
                            initial={{ opacity: 0, x: -20 }}
                            animate={{ opacity: 1, x: 0 }}
                            transition={{ delay: 0.3, duration: 0.5 }}
                        >
                            <label className="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                                ایمیل
                            </label>
                            <div className="relative">
                                <Mail className="absolute right-3 top-1/2 transform -translate-y-1/2 text-slate-400 w-5 h-5" />
                                <input
                                    {...register("email")}
                                    type="email"
                                    className={`w-full pl-12 pr-4 py-4 rounded-2xl border-2 transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-indigo-500/20 dark:bg-slate-700 dark:border-slate-600 dark:text-slate-100 ${
                                        errors.email 
                                            ? "border-red-400 focus:border-red-500 focus:ring-red-500/20" 
                                            : watchedValues.email && !errors.email
                                            ? "border-green-400 focus:border-green-500 focus:ring-green-500/20"
                                            : "border-slate-200 focus:border-indigo-500"
                                    }`}
                                    placeholder="example@domain.com"
                                    autoComplete="email"
                                />
                            </div>
                            <AnimatePresence>
                                {errors.email && (
                                    <motion.p 
                                        initial={{ opacity: 0, y: -10 }}
                                        animate={{ opacity: 1, y: 0 }}
                                        exit={{ opacity: 0, y: -10 }}
                                        className="mt-2 text-sm text-red-500 flex items-center gap-1"
                                    >
                                        <span className="w-1 h-1 bg-red-500 rounded-full"></span>
                                        {errors.email.message}
                                    </motion.p>
                                )}
                            </AnimatePresence>
                        </motion.div>

                        {/* Password Field */}
                        <motion.div
                            initial={{ opacity: 0, x: -20 }}
                            animate={{ opacity: 1, x: 0 }}
                            transition={{ delay: 0.4, duration: 0.5 }}
                        >
                            <label className="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                                رمز عبور
                            </label>
                            <div className="relative">
                                <Lock className="absolute right-3 top-1/2 transform -translate-y-1/2 text-slate-400 w-5 h-5" />
                                <input
                                    {...register("password")}
                                    type={showPassword ? "text" : "password"}
                                    className={`w-full pl-12 pr-12 py-4 rounded-2xl border-2 transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-indigo-500/20 dark:bg-slate-700 dark:border-slate-600 dark:text-slate-100 ${
                                        errors.password 
                                            ? "border-red-400 focus:border-red-500 focus:ring-red-500/20" 
                                            : watchedValues.password && !errors.password
                                            ? "border-green-400 focus:border-green-500 focus:ring-green-500/20"
                                            : "border-slate-200 focus:border-indigo-500"
                                    }`}
                                    placeholder="••••••••"
                                    autoComplete="current-password"
                                />
                                <button
                                    type="button"
                                    onClick={() => setShowPassword(!showPassword)}
                                    className="absolute left-3 top-1/2 transform -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors"
                                >
                                    {showPassword ? <EyeOff className="w-5 h-5" /> : <Eye className="w-5 h-5" />}
                                </button>
                            </div>
                            <AnimatePresence>
                                {errors.password && (
                                    <motion.p 
                                        initial={{ opacity: 0, y: -10 }}
                                        animate={{ opacity: 1, y: 0 }}
                                        exit={{ opacity: 0, y: -10 }}
                                        className="mt-2 text-sm text-red-500 flex items-center gap-1"
                                    >
                                        <span className="w-1 h-1 bg-red-500 rounded-full"></span>
                                        {errors.password.message}
                                    </motion.p>
                                )}
                            </AnimatePresence>
                        </motion.div>

                        {/* Server Error */}
                        <AnimatePresence>
                            {serverError && (
                                <motion.div
                                    initial={{ opacity: 0, scale: 0.95 }}
                                    animate={{ opacity: 1, scale: 1 }}
                                    exit={{ opacity: 0, scale: 0.95 }}
                                    className="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4"
                                >
                                    <p className="text-sm text-red-600 dark:text-red-400 text-center">
                                        {serverError}
                                    </p>
                                </motion.div>
                            )}
                        </AnimatePresence>

                        {/* Login Button */}
                        <motion.button
                            initial={{ opacity: 0, y: 20 }}
                            animate={{ opacity: 1, y: 0 }}
                            transition={{ delay: 0.5, duration: 0.5 }}
                            whileHover={{ scale: 1.02 }}
                            whileTap={{ scale: 0.98 }}
                            type="submit"
                            disabled={isSubmitting || loading}
                            className="w-full py-4 rounded-2xl bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold text-lg hover:from-indigo-700 hover:to-purple-700 transition-all duration-200 disabled:opacity-60 disabled:cursor-not-allowed shadow-lg hover:shadow-xl"
                        >
                            {loading ? (
                                <div className="flex items-center justify-center gap-2">
                                    <div className="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
                                    در حال ورود...
                                </div>
                            ) : (
                                "ورود"
                            )}
                        </motion.button>

                        {/* Divider */}
                        <motion.div
                            initial={{ opacity: 0 }}
                            animate={{ opacity: 1 }}
                            transition={{ delay: 0.6, duration: 0.5 }}
                            className="flex items-center gap-4 my-6"
                        >
                            <div className="flex-1 h-px bg-slate-200 dark:bg-slate-700" />
                            <div className="text-sm text-slate-500 dark:text-slate-400 px-2">
                                یا ورود با
                            </div>
                            <div className="flex-1 h-px bg-slate-200 dark:bg-slate-700" />
                        </motion.div>

                        {/* Social Buttons */}
                        <motion.div
                            initial={{ opacity: 0, y: 20 }}
                            animate={{ opacity: 1, y: 0 }}
                            transition={{ delay: 0.7, duration: 0.5 }}
                            className="grid grid-cols-2 gap-4"
                        >
                            <SocialButton
                                onClick={() => {
                                    window.location.href = "/api/auth/oauth/google";
                                }}
                                className="border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600"
                                icon={<FcGoogle className="w-5 h-5" />}
                            >
                                گوگل
                            </SocialButton>

                            <SocialButton
                                onClick={() => {
                                    window.location.href = "/api/auth/oauth/microsoft";
                                }}
                                className="border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600"
                                icon={<SiMicrosoft className="w-5 h-5" />}
                            >
                                مایکروسافت
                            </SocialButton>
                        </motion.div>

                        {/* Signup Link */}
                        <motion.div
                            initial={{ opacity: 0 }}
                            animate={{ opacity: 1 }}
                            transition={{ delay: 0.8, duration: 0.5 }}
                            className="text-center pt-4"
                        >
                            <span className="text-slate-600 dark:text-slate-400">
                                هنوز حساب ندارید؟{" "}
                            </span>
                            <a 
                                href="/signup" 
                                className="text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 font-medium hover:underline transition-colors"
                            >
                                ثبت نام
                            </a>
                        </motion.div>
                    </form>
                </div>
            </motion.div>
        </div>
    );
}
