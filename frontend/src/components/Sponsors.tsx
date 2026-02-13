import { Sponsor } from '@/types';
import Image from 'next/image';

interface SponsorsProps {
  sponsors: Sponsor[];
}

export default function Sponsors({ sponsors }: SponsorsProps) {
  if (!sponsors || sponsors.length === 0) return null;

  return (
    <section style={styles.section}>
      <div style={styles.container}>
        <div style={styles.grid}>
          {sponsors.map((sponsor) => (
            <a
              key={sponsor.id}
              href={sponsor.url}
              target="_blank"
              rel="noopener noreferrer"
              style={{
                ...styles.card,
                borderColor: sponsor.primary_color,
              }}
            >
              {sponsor.logo && (
                <div style={styles.logoWrapper}>
                  <Image
                    src={sponsor.logo}
                    alt={sponsor.name}
                    width={48}
                    height={48}
                    style={{ objectFit: 'contain', borderRadius: 6 }}
                  />
                </div>
              )}
              <div style={styles.content}>
                <span style={styles.name}>{sponsor.name}</span>
                {sponsor.promotions && sponsor.promotions.length > 0 && (
                  <span style={styles.promo}>{sponsor.promotions[0].highlight}</span>
                )}
              </div>
              <div style={styles.rating}>
                {'★'.repeat(sponsor.rating)}{'☆'.repeat(5 - sponsor.rating)}
              </div>
            </a>
          ))}
        </div>
      </div>
    </section>
  );
}

const styles: Record<string, React.CSSProperties> = {
  section: {
    background: '#1a1a2e',
    padding: '12px 0',
    borderBottom: '2px solid #16213e',
  },
  container: {
    maxWidth: 1200,
    margin: '0 auto',
    padding: '0 16px',
  },
  grid: {
    display: 'flex',
    gap: 12,
    overflowX: 'auto',
  },
  card: {
    display: 'flex',
    alignItems: 'center',
    gap: 10,
    background: '#16213e',
    borderRadius: 8,
    padding: '10px 16px',
    borderLeft: '3px solid',
    textDecoration: 'none',
    color: '#fff',
    flexShrink: 0,
    minWidth: 200,
  },
  logoWrapper: {
    flexShrink: 0,
    width: 48,
    height: 48,
    display: 'flex',
    alignItems: 'center',
    justifyContent: 'center',
  },
  content: {
    flex: 1,
    display: 'flex',
    flexDirection: 'column' as const,
    gap: 2,
    minWidth: 0,
  },
  name: {
    fontSize: 14,
    fontWeight: 700,
    textTransform: 'uppercase' as const,
  },
  promo: {
    fontSize: 12,
    color: '#ffd700',
    fontWeight: 600,
  },
  rating: {
    fontSize: 12,
    color: '#ffd700',
    flexShrink: 0,
  },
};
