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

#ifndef ZEND_THREADS_H
#define ZEND_THREADS_H

#include <stdbool.h>
#include <stdlib.h>
#include <Zend/zend_portability.h>

#ifdef ZEND_WIN32
# include <process.h>
# include <windows.h>
#else
# include <pthread.h>
#endif

typedef struct _zend_mutex {
#ifdef ZEND_WIN32
	CRITICAL_SECTION cs;
#else
	pthread_mutex_t mutex;
#endif
} zend_mutex;

typedef struct _zend_cond {
#ifdef ZEND_WIN32
	CONDITION_VARIABLE cond;
#else
	pthread_cond_t cond;
#endif
} zend_cond;

#ifdef ZEND_WIN32
typedef DWORD zend_thread_id;
#else
typedef pthread_t zend_thread_id;
#endif

zend_always_inline void zend_mutex_init(zend_mutex *mutex)
{
#ifdef ZEND_WIN32
	InitializeCriticalSection(&mutex->cs);
#else
	pthread_mutex_init(&mutex->mutex, NULL);
#endif
}

zend_always_inline void zend_mutex_destroy(zend_mutex *mutex)
{
#ifdef ZEND_WIN32
	DeleteCriticalSection(&mutex->cs);
#else
	pthread_mutex_destroy(&mutex->mutex);
#endif
}

zend_always_inline void zend_mutex_lock(zend_mutex *mutex)
{
#ifdef ZEND_WIN32
	EnterCriticalSection(&mutex->cs);
#else
	pthread_mutex_lock(&mutex->mutex);
#endif
}

zend_always_inline void zend_mutex_unlock(zend_mutex *mutex)
{
#ifdef ZEND_WIN32
	LeaveCriticalSection(&mutex->cs);
#else
	pthread_mutex_unlock(&mutex->mutex);
#endif
}

zend_always_inline zend_mutex *zend_mutex_alloc(void)
{
	zend_mutex *mutex = (zend_mutex *) malloc(sizeof(zend_mutex));
	if (mutex) {
		zend_mutex_init(mutex);
	}
	return mutex;
}

zend_always_inline void zend_mutex_free(zend_mutex *mutex)
{
	if (mutex) {
		zend_mutex_destroy(mutex);
		free(mutex);
	}
}

zend_always_inline void zend_cond_init(zend_cond *cond)
{
#ifdef ZEND_WIN32
	InitializeConditionVariable(&cond->cond);
#else
	pthread_cond_init(&cond->cond, NULL);
#endif
}

zend_always_inline void zend_cond_destroy(zend_cond *cond)
{
#ifdef ZEND_WIN32
	/* No destroy needed for CONDITION_VARIABLE on Windows */
#else
	pthread_cond_destroy(&cond->cond);
#endif
}

zend_always_inline void zend_cond_wait(zend_cond *cond, zend_mutex *mutex)
{
#ifdef ZEND_WIN32
	SleepConditionVariableCS(&cond->cond, &mutex->cs, INFINITE);
#else
	pthread_cond_wait(&cond->cond, &mutex->mutex);
#endif
}

zend_always_inline void zend_cond_broadcast(zend_cond *cond)
{
#ifdef ZEND_WIN32
	WakeAllConditionVariable(&cond->cond);
#else
	pthread_cond_broadcast(&cond->cond);
#endif
}

zend_always_inline zend_thread_id zend_thread_self(void)
{
#ifdef ZEND_WIN32
	return GetCurrentThreadId();
#else
	return pthread_self();
#endif
}

zend_always_inline bool zend_thread_equal(zend_thread_id t1, zend_thread_id t2)
{
#ifdef ZEND_WIN32
	return t1 == t2;
#else
	return pthread_equal(t1, t2);
#endif
}

#endif /* ZEND_THREADS_H */
