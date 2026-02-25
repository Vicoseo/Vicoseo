'use client';

import { useState } from 'react';
import type { SiteEarning } from '@/types';

interface Props {
  earnings: SiteEarning[];
  domain: string;
}

function resolveUrl(url: string, domain: string) {
  if (!url) return '';
  if (url.startsWith('http')) return url;
  return `https://${domain}${url}`;
}

function extractAmount(title: string | null): string | null {
  if (!title) return null;
  const match = title.match(/([\d.]+)\s*TL/);
  return match ? match[1] + ' TL' : null;
}

export default function EarningsSection({ earnings, domain }: Props) {
  const [openId, setOpenId] = useState<number | null>(null);
  const openItem = earnings.find((e) => e.id === openId) || null;

  if (!earnings || earnings.length === 0) return null;

  return (
    <div style={{ margin: '20px auto', maxWidth: 1200, padding: '0 16px' }}>
      <h2 style={{
        fontSize: '16px',
        fontWeight: 600,
        marginBottom: '12px',
        opacity: 0.85,
      }}>
        Yüksek Kazançlar
      </h2>

      <div style={{
        display: 'flex',
        gap: '10px',
        overflowX: 'auto',
        paddingBottom: '6px',
        scrollbarWidth: 'thin',
      }}>
        {earnings.map((item) => {
          const amount = extractAmount(item.title);
          const isOpen = openId === item.id;
          return (
            <div
              key={item.id}
              onClick={() => setOpenId(isOpen ? null : item.id)}
              style={{
                flex: '0 0 auto',
                width: 110,
                cursor: 'pointer',
                borderRadius: '8px',
                overflow: 'hidden',
                border: isOpen ? '2px solid #f59e0b' : '1px solid rgba(255,255,255,0.1)',
                background: isOpen ? 'rgba(245,158,11,0.08)' : 'rgba(255,255,255,0.03)',
                transition: 'all 0.15s ease',
              }}
            >
              {item.image && (
                <img
                  src={resolveUrl(item.image, domain)}
                  alt={item.title || 'Kazanç'}
                  loading="lazy"
                  style={{
                    width: '100%',
                    aspectRatio: '4/3',
                    objectFit: 'cover',
                    display: 'block',
                  }}
                />
              )}
              <div style={{
                padding: '6px 8px',
                textAlign: 'center',
              }}>
                {amount && (
                  <div style={{
                    fontSize: '13px',
                    fontWeight: 700,
                    color: '#f59e0b',
                    lineHeight: 1.2,
                  }}>
                    {amount}
                  </div>
                )}
                <div style={{
                  fontSize: '10px',
                  opacity: 0.6,
                  lineHeight: 1.3,
                  marginTop: '2px',
                  whiteSpace: 'nowrap',
                  overflow: 'hidden',
                  textOverflow: 'ellipsis',
                }}>
                  {item.title?.replace(/\s*ile\s*[\d.]+\s*TL\s*Kazanç!?/i, '') || 'Kazanç'}
                </div>
              </div>
            </div>
          );
        })}
      </div>

      {openItem && (
        <div style={{
          marginTop: '10px',
          padding: '14px 16px',
          borderRadius: '8px',
          background: 'rgba(255,255,255,0.04)',
          borderLeft: '3px solid #f59e0b',
          fontSize: '14px',
          lineHeight: 1.7,
          opacity: 0.9,
        }}>
          {openItem.content && (
            <div
              dangerouslySetInnerHTML={{
                __html: openItem.content.replace(/<img(?![^>]*loading=)/gi, '<img loading="lazy"'),
              }}
            />
          )}
          {openItem.video_url && (
            <div style={{ marginTop: '10px' }}>
              <a
                href={openItem.video_url}
                target="_blank"
                rel="noopener noreferrer"
                style={{
                  display: 'inline-block',
                  padding: '7px 20px',
                  background: '#f59e0b',
                  color: '#000',
                  borderRadius: '6px',
                  textDecoration: 'none',
                  fontWeight: 600,
                  fontSize: '13px',
                }}
              >
                Videoyu İzle
              </a>
            </div>
          )}
        </div>
      )}
    </div>
  );
}
