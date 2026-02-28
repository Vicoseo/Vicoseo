const services = [
  {
    title: 'Spor Bahisleri',
    description: '30+ spor dalında yüksek oranlarla canlı ve maç öncesi bahis yapın.',
    icon: (
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round">
        <circle cx="12" cy="12" r="10" />
        <path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20" />
        <path d="M2 12h20" />
      </svg>
    ),
  },
  {
    title: 'Canlı Casino',
    description: 'Gerçek krupiyelerle rulet, blackjack ve poker masalarında oynayın.',
    icon: (
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round">
        <rect x="2" y="4" width="20" height="16" rx="3" />
        <path d="M12 8v8M8 12h8" />
        <circle cx="7" cy="8" r="1" fill="currentColor" />
        <circle cx="17" cy="16" r="1" fill="currentColor" />
      </svg>
    ),
  },
  {
    title: 'Slot Oyunları',
    description: "2.000+ slot oyunu, jackpot fırsatları ve freespin kampanyaları.",
    icon: (
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round">
        <rect x="3" y="3" width="18" height="18" rx="3" />
        <path d="M3 9h18M3 15h18M9 3v18M15 3v18" />
      </svg>
    ),
  },
  {
    title: '7/24 Destek',
    description: 'Profesyonel müşteri hizmetleri ekibimiz her an yanınızda.',
    icon: (
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round">
        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
        <path d="M8 10h.01M12 10h.01M16 10h.01" />
      </svg>
    ),
  },
];

export default function ServiceCards() {
  return (
    <section className="service-cards">
      <h2>Hizmetlerimiz</h2>
      <div className="service-cards-grid">
        {services.map((service) => (
          <div key={service.title} className="service-card">
            <div className="service-card-icon">{service.icon}</div>
            <h3>{service.title}</h3>
            <p>{service.description}</p>
          </div>
        ))}
      </div>
    </section>
  );
}
