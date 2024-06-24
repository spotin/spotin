import { User } from '@/users/types/user';

export type ReadUser = Omit<User, 'password'>;
