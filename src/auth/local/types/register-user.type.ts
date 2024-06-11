import { User } from '@prisma/client';

export type RegisterUser = Pick<User, 'username' | 'email'>;
