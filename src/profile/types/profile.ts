import { User } from '@/users/types/user';

export type Profile = Pick<User, 'username' | 'email'>;
