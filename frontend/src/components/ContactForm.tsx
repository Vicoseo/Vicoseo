'use client';

import { useState, FormEvent } from 'react';

interface ContactFormProps {
  domain: string;
}

export default function ContactForm({ domain }: ContactFormProps) {
  const [formData, setFormData] = useState({
    name: '',
    email: '',
    subject: 'Genel',
    message: '',
    website: '', // honeypot
  });
  const [status, setStatus] = useState<'idle' | 'sending' | 'success' | 'error'>('idle');
  const [errorMessage, setErrorMessage] = useState('');

  const handleSubmit = async (e: FormEvent) => {
    e.preventDefault();

    // Honeypot check
    if (formData.website) return;

    setStatus('sending');
    setErrorMessage('');

    try {
      const apiUrl = process.env.NEXT_PUBLIC_API_URL || 'http://localhost:8000/api/v1';
      const res = await fetch(`${apiUrl}/contact`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-Tenant-Domain': domain,
        },
        body: JSON.stringify({
          name: formData.name,
          email: formData.email,
          subject: formData.subject,
          message: formData.message,
        }),
      });

      if (res.ok) {
        setStatus('success');
        setFormData({ name: '', email: '', subject: 'Genel', message: '', website: '' });
      } else {
        const data = await res.json().catch(() => null);
        if (res.status === 429) {
          setErrorMessage('Çok fazla mesaj gönderdiniz. Lütfen daha sonra tekrar deneyin.');
        } else if (data?.errors) {
          const firstError = Object.values(data.errors)[0];
          setErrorMessage(Array.isArray(firstError) ? firstError[0] as string : 'Bir hata oluştu.');
        } else {
          setErrorMessage('Mesaj gönderilemedi. Lütfen tekrar deneyin.');
        }
        setStatus('error');
      }
    } catch {
      setErrorMessage('Bağlantı hatası. Lütfen tekrar deneyin.');
      setStatus('error');
    }
  };

  if (status === 'success') {
    return (
      <div className="contact-form-success">
        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#22c55e" strokeWidth="2">
          <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
          <polyline points="22 4 12 14.01 9 11.01" />
        </svg>
        <h3>Mesajınız Gönderildi</h3>
        <p>En kısa sürede size geri dönüş yapacağız. Teşekkür ederiz.</p>
        <button
          className="contact-form-btn"
          onClick={() => setStatus('idle')}
        >
          Yeni Mesaj Gönder
        </button>
      </div>
    );
  }

  return (
    <form className="contact-form" onSubmit={handleSubmit} noValidate>
      <div className="contact-form-row">
        <div className="contact-form-field">
          <label htmlFor="contact-name">Ad Soyad *</label>
          <input
            id="contact-name"
            type="text"
            required
            minLength={2}
            maxLength={100}
            value={formData.name}
            onChange={(e) => setFormData({ ...formData, name: e.target.value })}
            placeholder="Adınız ve soyadınız"
          />
        </div>
        <div className="contact-form-field">
          <label htmlFor="contact-email">E-posta *</label>
          <input
            id="contact-email"
            type="email"
            required
            maxLength={150}
            value={formData.email}
            onChange={(e) => setFormData({ ...formData, email: e.target.value })}
            placeholder="ornek@email.com"
          />
        </div>
      </div>

      <div className="contact-form-field">
        <label htmlFor="contact-subject">Konu *</label>
        <select
          id="contact-subject"
          required
          value={formData.subject}
          onChange={(e) => setFormData({ ...formData, subject: e.target.value })}
        >
          <option value="Genel">Genel Soru</option>
          <option value="Teknik">Teknik Destek</option>
          <option value="Bonus">Bonus & Kampanya</option>
          <option value="Şikayet">Şikayet & Öneri</option>
        </select>
      </div>

      <div className="contact-form-field">
        <label htmlFor="contact-message">Mesajınız *</label>
        <textarea
          id="contact-message"
          required
          minLength={10}
          maxLength={2000}
          rows={5}
          value={formData.message}
          onChange={(e) => setFormData({ ...formData, message: e.target.value })}
          placeholder="Mesajınızı buraya yazın..."
        />
      </div>

      {/* Honeypot - hidden from users */}
      <div style={{ position: 'absolute', left: '-9999px', opacity: 0 }} aria-hidden="true">
        <input
          type="text"
          name="website"
          tabIndex={-1}
          autoComplete="off"
          value={formData.website}
          onChange={(e) => setFormData({ ...formData, website: e.target.value })}
        />
      </div>

      {status === 'error' && errorMessage && (
        <div className="contact-form-error">{errorMessage}</div>
      )}

      <button
        type="submit"
        className="contact-form-btn"
        disabled={status === 'sending'}
      >
        {status === 'sending' ? 'Gönderiliyor...' : 'Mesaj Gönder'}
      </button>
    </form>
  );
}
