import { headers } from 'next/headers';

export async function getCurrentDomain(): Promise<string> {
  const headersList = await headers();
  const host = headersList.get('host') || 'localhost';
  // Strip port number
  return host.split(':')[0];
}
