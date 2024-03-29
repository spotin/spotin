import { PrismaClient, UserRole } from '@prisma/client';
import * as bcrypt from 'bcrypt';

const prisma = new PrismaClient();

async function main() {
  const admin = await prisma.user.upsert({
    where: { username: 'admin' },
    update: {},
    create: {
      username: 'admin',
      email: process.env.SPOT_IN_ADMIN_EMAIL as string,
      password: await bcrypt.hashSync(
        process.env.SPOT_IN_ADMIN_PASSWORD as string,
        bcrypt.genSaltSync(10),
      ),
      enabled: true,
      role: UserRole.ADMIN,
    },
  });

  const spot1 = await prisma.spot.upsert({
    where: { id: '00000000-0000-0000-0000-000000000001' },
    update: {},
    create: {
      id: '00000000-0000-0000-0000-000000000001',
      title: 'spot title',
      description: 'spot description',
      latitude: 47.13665452584392,
      longitude: 7.2462789951019815,
      redirection: 'https://www.biel-bienne.ch/',
      referenced: true,
      configured: true,
      userId: admin.id,
    },
  });

  const spot2 = await prisma.spot.upsert({
    where: { id: '00000000-0000-0000-0000-000000000002' },
    update: {},
    create: {
      id: '00000000-0000-0000-0000-000000000002',
      configured: false,
      userId: admin.id,
    },
  });

  console.log({ admin, spot1, spot2 });
}

main()
  .then(async () => {
    await prisma.$disconnect();
  })
  .catch(async (e) => {
    console.error(e);
    await prisma.$disconnect();
    process.exit(1);
  });
