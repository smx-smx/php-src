/*
   +----------------------------------------------------------------------+
   | Zend Engine                                                          |
   +----------------------------------------------------------------------+
   | Copyright (c) Zend Technologies Ltd. (http://www.zend.com)           |
   +----------------------------------------------------------------------+
   | This source file is subject to version 2.00 of the Zend license,     |
   | that is bundled with this package in the file LICENSE, and is        |
   | available through the world-wide-web at the following url:           |
   | http://www.zend.com/license/2_00.txt.                                |
   | If you did not receive a copy of the Zend license and are unable to  |
   | obtain it through the world-wide-web, please send a note to          |
   | license@zend.com so we can mail you a copy immediately.              |
   +----------------------------------------------------------------------+
*/

#ifndef ZEND_INLINE_H
#define ZEND_INLINE_H

#if ZEND_DEBUG || defined(ZEND_WIN32_NEVER_INLINE)
# ifndef zend_always_inline
#  define zend_always_inline inline
# endif
# ifndef zend_never_inline
#  define zend_never_inline
# endif
#else
# if defined(__GNUC__)
#  if __GNUC__ >= 3
#   ifndef zend_always_inline
#    define zend_always_inline inline __attribute__((always_inline))
#   endif
#   ifndef zend_never_inline
#    define zend_never_inline __attribute__((noinline))
#   endif
#  else
#   ifndef zend_always_inline
#    define zend_always_inline inline
#   endif
#   ifndef zend_never_inline
#    define zend_never_inline
#   endif
#  endif
# elif defined(_MSC_VER)
#  ifndef zend_always_inline
#   define zend_always_inline __forceinline
#  endif
#  ifndef zend_never_inline
#   define zend_never_inline __declspec(noinline)
#  endif
# else
#  if __has_attribute(always_inline)
#   ifndef zend_always_inline
#    define zend_always_inline inline __attribute__((always_inline))
#   endif
#  else
#   ifndef zend_always_inline
#    define zend_always_inline inline
#   endif
#  endif
#  if __has_attribute(noinline)
#   ifndef zend_never_inline
#    define zend_never_inline __attribute__((noinline))
#   endif
#  else
#   ifndef zend_never_inline
#    define zend_never_inline
#   endif
#  endif
# endif
#endif /* ZEND_DEBUG */

#endif /* ZEND_INLINE_H */
