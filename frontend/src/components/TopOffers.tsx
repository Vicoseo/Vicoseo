import { TopOffer } from '@/types';

interface TopOffersProps {
  offers: TopOffer[];
}

export default function TopOffers({ offers }: TopOffersProps) {
  if (!offers || offers.length === 0) return null;

  return (
    <section style={styles.section}>
      <div style={styles.container}>
        <h2 style={styles.title}>Top Offers</h2>
        <div style={styles.grid}>
          {offers.map((offer) => (
            <div key={`${offer.source}-${offer.id}`} style={styles.card}>
              <div style={styles.logoWrapper}>
                <img
                  src={offer.logo_url}
                  alt={offer.bonus_text}
                  width={120}
                  height={60}
                  style={{ objectFit: 'contain', maxWidth: '100%', maxHeight: '100%' }}
                  loading="lazy"
                />
              </div>
              <div style={styles.content}>
                <p style={styles.bonus}>{offer.bonus_text}</p>
                <a
                  href={offer.target_url}
                  style={styles.cta}
                  target="_blank"
                  rel="noopener noreferrer"
                >
                  {offer.cta_text}
                </a>
              </div>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
}

const styles: Record<string, React.CSSProperties> = {
  section: {
    background: '#f8f9fa',
    padding: '24px 0',
    borderBottom: '1px solid #e9ecef',
  },
  container: {
    maxWidth: 1200,
    margin: '0 auto',
    padding: '0 16px',
  },
  title: {
    fontSize: 18,
    fontWeight: 700,
    marginBottom: 16,
    color: '#333',
  },
  grid: {
    display: 'grid',
    gridTemplateColumns: 'repeat(auto-fill, minmax(280px, 1fr))',
    gap: 16,
  },
  card: {
    display: 'flex',
    alignItems: 'center',
    gap: 16,
    background: '#fff',
    borderRadius: 8,
    padding: 16,
    boxShadow: '0 1px 3px rgba(0,0,0,0.1)',
  },
  logoWrapper: {
    flexShrink: 0,
    width: 120,
    height: 60,
    display: 'flex',
    alignItems: 'center',
    justifyContent: 'center',
  },
  content: {
    flex: 1,
    minWidth: 0,
  },
  bonus: {
    fontSize: 14,
    fontWeight: 600,
    color: '#333',
    marginBottom: 8,
  },
  cta: {
    display: 'inline-block',
    padding: '8px 20px',
    background: 'var(--primary-color, #007bff)',
    color: '#fff',
    textDecoration: 'none',
    borderRadius: 4,
    fontSize: 13,
    fontWeight: 600,
  },
};
