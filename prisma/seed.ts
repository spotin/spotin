import { PrismaClient, UserRole } from '@prisma/client';
import * as argon2id from 'argon2';

const prisma = new PrismaClient();

async function main() {
	const admin = await prisma.user.upsert({
		where: {
			email: process.env.SPOT_IN_ADMIN_EMAIL as string,
			username: 'Spot in',
		},
		update: {},
		create: {
			username: 'Spot in',
			email: process.env.SPOT_IN_ADMIN_EMAIL as string,
			password: await argon2id.hash(
				process.env.SPOT_IN_ADMIN_PASSWORD as string,
			),
			enabled: true,
			role: UserRole.ADMIN,
		},
	});

	await prisma.spot.upsert({
		where: { id: '00000000-0000-0000-0000-000000000001' },
		update: {},
		create: {
			id: '00000000-0000-0000-0000-000000000001',
			name: 'spot name',
			description: 'spot description',
			latitude: 47.13665452584392,
			longitude: 7.2462789951019815,
			websiteTarget: 'https://www.biel-bienne.ch/',
			public: true,
			configured: true,
			directAccessToWebsiteTarget: false,
			userId: admin.id,
		},
	});

	await prisma.spot.upsert({
		where: { id: '00000000-0000-0000-0000-000000000002' },
		update: {},
		create: {
			id: '00000000-0000-0000-0000-000000000002',
			configured: false,
			directAccessToWebsiteTarget: false,
			userId: admin.id,
		},
	});

	console.log('Database seeded with default data');
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
