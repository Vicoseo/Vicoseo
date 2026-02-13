import Link from 'next/link';

export default function NotFound() {
  return (
    <div className="not-found">
      <div className="not-found__code">404</div>
      <p className="not-found__message">
        The page you are looking for could not be found.
      </p>
      <Link href="/" className="not-found__link">
        Go back home
      </Link>
    </div>
  );
}
