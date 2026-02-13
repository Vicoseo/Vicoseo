interface EntryBarProps {
  url: string;
  siteName: string;
  primaryColor: string;
}

export default function EntryBar({ url, siteName, primaryColor }: EntryBarProps) {
  return (
    <div style={styles.bar}>
      <div style={styles.container}>
        <a
          href={url}
          target="_blank"
          rel="noopener noreferrer"
          style={{
            ...styles.button,
            background: primaryColor,
          }}
        >
          {siteName} &mdash; Siteye Giriş Yap →
        </a>
      </div>
    </div>
  );
}

const styles: Record<string, React.CSSProperties> = {
  bar: {
    background: '#0d0d1a',
    padding: '8px 0',
    borderBottom: '1px solid #1a1a2e',
  },
  container: {
    maxWidth: 1200,
    margin: '0 auto',
    padding: '0 16px',
    display: 'flex',
    justifyContent: 'center',
  },
  button: {
    display: 'inline-block',
    color: '#fff',
    fontWeight: 700,
    fontSize: 14,
    padding: '8px 24px',
    borderRadius: 6,
    textDecoration: 'none',
    letterSpacing: 0.5,
  },
};
